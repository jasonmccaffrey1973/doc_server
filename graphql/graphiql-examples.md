# GraphiQL examples

Open GraphiQL at `/graphiql` and use the GraphQL endpoint `/graphql`.

## Auth flow

### 1) Register

```graphql
mutation Register {
  register(
    name: "Jane Graph"
    username: "janegraphql"
    email: "jane.graphql@example.com"
    password: "password"
    password_confirmation: "password"
    device_name: "react-dev"
  ) {
    token
    user {
      id
      name
      username
      email
    }
  }
}
```

### 2) Login

```graphql
mutation Login {
  login(
    login: "janegraphql"
    password: "password"
    device_name: "react-dev"
  ) {
    token
    user {
      id
      username
      email
    }
  }
}
```

### 3) Set bearer token header

In GraphiQL, open Headers and paste:

```json
{
  "Authorization": "Bearer YOUR_TOKEN_HERE"
}
```

### 4) Current user

```graphql
query Me {
  me {
    id
    name
    username
    email
    email_verified_at
  }
}
```

## LMS authoring workflow

Run these in order. Save ids from responses for the next steps.

### 1) Create a course

```graphql
mutation CreateCourse {
  createCourse(
    input: {
      title: "SCORM Safety Basics"
      description: "Introductory course for safety training"
      status: "draft"
    }
  ) {
    id
    title
    status
    created_at
  }
}
```

### 2) Create a chapter in the course

```graphql
mutation CreateChapter {
  createChapter(
    input: {
      course_id: "COURSE_ID"
      title: "Chapter 1 - Orientation"
      description: "Welcome and orientation"
      position: 1
    }
  ) {
    id
    course_id
    title
    position
  }
}
```

### 3) Create a reusable library lesson

```graphql
mutation CreateLesson {
  createLesson(
    input: {
      title: "Library Lesson - PPE Overview"
      content: {
        type: "doc"
        content: [
          {
            type: "paragraph"
            content: [{ type: "text", text: "Always inspect PPE before use." }]
          }
        ]
      }
    }
  ) {
    id
    title
    content_version
    content
  }
}
```

### 4) Clone lesson into a course-specific copy

```graphql
mutation AddLessonToCourse {
  addLessonToCourse(input: { course_id: "COURSE_ID", lesson_id: "LESSON_ID" }) {
    id
    course_id
    lesson_id
    title
    source_version
    content
  }
}
```

### 5) Edit the course-specific lesson copy

```graphql
mutation UpdateCourseLesson {
  updateCourseLesson(
    id: "COURSE_LESSON_ID"
    input: {
      title: "Course Copy - PPE Overview"
      content: {
        type: "doc"
        content: [
          {
            type: "paragraph"
            content: [
              { type: "text", text: "This is course-specific lesson content." }
            ]
          }
        ]
      }
    }
  ) {
    id
    title
    source_version
    content
  }
}
```

### 6) Place course lesson in chapter

```graphql
mutation PlaceCourseLessonInChapter {
  placeCourseLessonInChapter(
    input: {
      chapter_id: "CHAPTER_ID"
      course_lesson_id: "COURSE_LESSON_ID"
      position: 1
    }
  ) {
    chapter_id
    course_lesson_id
    position
  }
}
```

## LMS queries

### 1) List courses

```graphql
query Courses {
  courses(status: "draft", limit: 25, offset: 0) {
    id
    title
    status
    chapters {
      id
      title
      position
    }
  }
}
```

### 2) Get one course with nested data

```graphql
query Course {
  course(id: "COURSE_ID") {
    id
    title
    description
    status
    chapters {
      id
      title
      position
      courseLessons {
        id
        title
      }
    }
    courseLessons {
      id
      title
      lesson {
        id
        title
      }
    }
  }
}
```

### 3) List lesson library

```graphql
query Lessons {
  lessons(search: "PPE", limit: 25, offset: 0) {
    id
    title
    content_version
  }
}
```

### 4) Get one lesson

```graphql
query Lesson {
  lesson(id: "LESSON_ID") {
    id
    title
    content_version
    content
  }
}
```

### 5) List course lesson copies by course

```graphql
query CourseLessons {
  courseLessons(course_id: "COURSE_ID") {
    id
    title
    source_version
    lesson {
      id
      title
    }
  }
}
```

### 6) List chapter placements

```graphql
query ChapterLessons {
  chapterLessons(chapter_id: "CHAPTER_ID") {
    chapter_id
    course_lesson_id
    position
    courseLesson {
      id
      title
    }
  }
}
```

## LMS update and delete operations

### 1) Update course

```graphql
mutation UpdateCourse {
  updateCourse(
    id: "COURSE_ID"
    input: {
      title: "SCORM Safety Basics v2"
      description: "Updated description"
      status: "published"
    }
  ) {
    id
    title
    status
  }
}
```

### 2) Update chapter

```graphql
mutation UpdateChapter {
  updateChapter(
    id: "CHAPTER_ID"
    input: {
      title: "Chapter 1 - Course Orientation"
      description: "Updated chapter intro"
      position: 1
    }
  ) {
    id
    title
    position
  }
}
```

### 3) Update library lesson

```graphql
mutation UpdateLesson {
  updateLesson(
    id: "LESSON_ID"
    input: {
      title: "Library Lesson - PPE Overview Updated"
      content: {
        type: "doc"
        content: [
          {
            type: "paragraph"
            content: [
              { type: "text", text: "Updated reusable lesson content." }
            ]
          }
        ]
      }
    }
  ) {
    id
    title
    content_version
  }
}
```

### 4) Remove lesson placement from chapter

```graphql
mutation RemoveCourseLessonFromChapter {
  removeCourseLessonFromChapter(
    chapter_id: "CHAPTER_ID"
    course_lesson_id: "COURSE_LESSON_ID"
  )
}
```

### 5) Delete chapter

```graphql
mutation DeleteChapter {
  deleteChapter(id: "CHAPTER_ID")
}
```

### 6) Delete course

```graphql
mutation DeleteCourse {
  deleteCourse(id: "COURSE_ID")
}
```

### 7) Delete library lesson

```graphql
mutation DeleteLesson {
  deleteLesson(id: "LESSON_ID")
}
```

## Media management

### 1) List media by kind

```graphql
query ListMedia {
  listMedia(kind: "image", limit: 25, offset: 0) {
    items {
      id
      kind
      name
      url
      mimeType
      size
      altText
      createdAt
    }
    total
  }
}
```

Supported kinds: `image`, `video`, `audio`. Omit `kind` to list all media types.

### 2) Upload single media file

In GraphiQL, use the file picker to select a file, then run:

```graphql
mutation UploadMedia {
  uploadMedia(
    kind: "image"
    file: null
    altText: "Alternative text for accessibility"
    storageLocation: null
  ) {
    id
    kind
    name
    url
    mimeType
    size
    altText
    createdAt
  }
}
```

Supported kinds: `image`, `video`, `audio`. Leave `storageLocation` null to use default storage.

### 3) Upload media from URL

```graphql
mutation UploadMediaFromUrl {
  uploadMediaFromUrl(
    kind: "image"
    url: "https://example.com/image.jpg"
    altText: "Downloaded image"
    storageLocation: null
  ) {
    id
    kind
    name
    url
    mimeType
    size
    altText
    createdAt
  }
}
```

### 4) Bulk upload media files

```graphql
mutation BulkUploadMedia {
  bulkUploadMedia(
    kind: "image"
    files: [null, null]
    altText: "Bulk uploaded images"
    storageLocation: null
  ) {
    id
    kind
    name
    url
    mimeType
    size
    createdAt
  }
}
```

Max 50 files per request. Leave `storageLocation` null to use default storage.

### 5) Delete media

```graphql
mutation DeleteMedia {
  deleteMedia(ids: ["MEDIA_ID_1", "MEDIA_ID_2"]) {
    success
    deletedCount
  }
}
```

Delete one or multiple media files at once.
For one item, GraphQL also accepts a single ID: `deleteMedia(ids: "MEDIA_ID")`.

### 6) Update storage location

```graphql
mutation UpdateStorageLocation {
  updateStorageLocation(location: "STORAGE_LOCATION_ID") {
    success
    location
  }
}
```

Set the default storage location for future uploads.

## Logout

```graphql
mutation Logout {
  logout
}
```
