# Media Manager Implementation Guide

## Overview

The media manager system provides GraphQL endpoints for uploading, managing, and deleting media files (images, videos, and audio). It supports both local filesystem storage and cloud storage providers (S3, Google Cloud Storage).

## Architecture

### Backend Components

#### Database Models
- **Media**: Stores metadata about uploaded files
  - `id` - UUID primary key
  - `filename` - Generated filename
  - `original_name` - User-provided filename
  - `media_type` - Type of media (image, video, audio)
  - `size` - File size in bytes
  - `path` - Storage path
  - `storage_location_id` - Reference to storage location
  - `user_id` - Reference to uploading user

- **StorageLocation**: Defines where media files are stored
  - `id` - UUID primary key
  - `name` - Display name
  - `type` - Storage type (local, s3, gcs)
  - `configuration` - JSON configuration (bucket credentials, etc.)
  - `is_default` - Flag to mark default storage location

#### GraphQL Mutations

##### `uploadMedia(input: UploadMediaInput!): Media!`
Upload a single media file.

**Input Parameters:**
```graphql
input UploadMediaInput {
  file: Upload!           # The file to upload
  mediaType: String!      # "image", "video", or "audio"
  storageLocationId: ID   # Optional - defaults to default storage location
}
```

**Response:**
```graphql
type Media {
  id: ID!
  filename: String!
  original_name: String!
  media_type: String!
  size: Int!
  path: String!
  url: String!                      # Full URL to access the file
  storage_location_id: ID
  storageLocation: StorageLocation
  user_id: ID
  created_at: DateTime!
  updated_at: DateTime!
}
```

##### `bulkUploadMedia(input: BulkUploadMediaInput!): [Media!]!`
Upload multiple media files at once (up to 50 files, 500MB each).

**Input Parameters:**
```graphql
input BulkUploadMediaInput {
  files: [Upload!]!       # Array of files to upload
  mediaType: String!      # "image", "video", or "audio"
  storageLocationId: ID   # Optional - defaults to default storage location
}
```

##### `deleteMedia(id: ID!): Boolean!`
Delete a media file by ID.

##### `configureStorageLocation(input: ConfigureStorageLocationInput!): StorageLocation!`
Configure a new storage location.

**Input Parameters:**
```graphql
input ConfigureStorageLocationInput {
  name: String!          # Unique name for this storage location
  type: String!          # "local", "s3", or "gcs"
  configuration: JSON    # Storage-specific configuration
  isDefault: Boolean     # Set as default storage location
}
```

**Configuration Examples:**

Local storage:
```json
{
  "disk": "local"
}
```

AWS S3:
```json
{
  "key": "your-aws-key",
  "secret": "your-aws-secret",
  "region": "us-east-1",
  "bucket": "your-bucket",
  "bucket_url": "https://your-bucket.s3.amazonaws.com"
}
```

Google Cloud Storage:
```json
{
  "project_id": "your-project-id",
  "bucket": "your-bucket",
  "bucket_url": "https://storage.googleapis.com/your-bucket"
}
```

### Frontend Components

#### React Hook: `useMediaManager`

**Returns:**
```typescript
{
  MEDIA_TYPES: { IMAGE: "IMAGE", VIDEO: "VIDEO", AUDIO: "AUDIO" }
  selectedTab: MediaTab              // Currently selected media type
  selectTab: (tab: string) => void   // Change selected media type
  ribbonIcons: RibbonIcon[]          // Icons for current media type
  performRibbonAction: {
    'add': () => void                // Upload single file
    'bulk': () => void               // Upload multiple files
    'view': () => void               // View media (to implement)
    'edit': () => void               // Edit media (to implement)
    'delete': () => void             // Delete media (to implement)
  }
  isUploading: boolean               // Loading state
  uploadError: string | null         // Error message if upload failed
}
```

**Usage Example:**
```tsx
import useMediaManager from './hooks/useMediaManager';

export function MediaManagerComponent() {
  const {
    MEDIA_TYPES,
    selectedTab,
    selectTab,
    ribbonIcons,
    performRibbonAction,
    isUploading,
    uploadError,
  } = useMediaManager();

  return (
    <div>
      <div className="tabs">
        {Object.values(MEDIA_TYPES).map(type => (
          <button
            key={type}
            onClick={() => selectTab(type)}
            className={selectedTab === type ? 'active' : ''}
          >
            {type}
          </button>
        ))}
      </div>

      <div className="ribbon-actions">
        {ribbonIcons.map(icon => (
          <button
            key={icon.id}
            onClick={() => performRibbonAction[icon.action]?.()}
            disabled={isUploading}
          >
            {icon.name}
          </button>
        ))}
      </div>

      {uploadError && <div className="error">{uploadError}</div>}
      {isUploading && <div className="loading">Uploading...</div>}
    </div>
  );
}
```

## Setup Instructions

### 1. Database Setup

Run the migrations to create the necessary tables:
```bash
php artisan migrate
```

### 2. Configure Default Storage Location

Create a default storage location (run these migrations or use a seeder):
```php
StorageLocation::create([
    'name' => 'Local Storage',
    'type' => 'local',
    'configuration' => ['disk' => 'local'],
    'is_default' => true,
]);
```

### 3. Configure File Storage

Update your `.env` file:
```env
FILESYSTEM_DISK=local
```

For S3, also add:
```env
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket
```

### 4. Update Apollo Client

Ensure your Apollo Client configuration includes support for file uploads:
```tsx
import { ApolloClient, InMemoryCache, createHttpLink } from '@apollo/client';
import { createUploadLink } from 'apollo-upload-client';

const link = createUploadLink({
  uri: '/graphql',
  credentials: 'include',
});

const client = new ApolloClient({
  link,
  cache: new InMemoryCache(),
});
```

### 5. Configure Lighthouse for File Uploads

Ensure your Laravel Lighthouse configuration (`config/lighthouse.php`) has:
```php
'multipart' => true,
```

## File Organization

Uploaded files are organized by:
- Media type: `media/{type}/`
- Date: `media/{type}/YYYY/MM/DD/`
- Random filename: `{32-char-random-uuid}.{extension}`

Example: `media/image/2024/01/15/a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6.jpg`

## Validation

- **File Size**: 500MB maximum per file
- **Bulk Upload**: Maximum 50 files per request
- **Media Types**: image, video, or audio
- **Storage Location**: Must exist and be properly configured

## Error Handling

Common error scenarios:

| Error | Cause | Solution |
|-------|-------|----------|
| "No default storage location configured" | No default storage location set | Run seeder or configure one via GraphQL |
| "Unsupported storage type" | Invalid storage type | Use local, s3, or gcs |
| "File size exceeds limit" | File > 500MB | Upload smaller files |
| "Too many files in bulk upload" | > 50 files in one request | Split into smaller batches |

## Performance Considerations

- **Bulk uploads** are processed sequentially but can be parallelized in future versions
- **Storage queries** use indexing on frequently filtered columns
- **Media URL generation** is cached in the model attributes
- Consider implementing **image resizing** in a background job for image uploads

## Security Considerations

1. **Authentication**: All mutations require authenticated user
2. **Authorization**: Users can only delete their own media files (implement in deleteMedia mutation)
3. **File validation**: Validate MIME types, not just extensions
4. **Path traversal**: Use random filenames to prevent directory traversal
5. **Quarantine**: Consider scanning uploads for malware before storing

## Future Enhancements

- [ ] Image resizing and optimization
- [ ] Video thumbnail generation
- [ ] Audio metadata extraction
- [ ] Virus/malware scanning
- [ ] Rate limiting on uploads
- [ ] Batch delete operation
- [ ] Media search/filtering
- [ ] Access control lists per file
- [ ] CDN integration
- [ ] Resumable uploads for large files
