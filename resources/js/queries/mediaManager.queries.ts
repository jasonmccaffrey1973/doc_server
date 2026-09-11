import { gql, useMutation } from "@apollo/client";

// GraphQL Mutations
const UPLOAD_MEDIA = gql`
  mutation UploadMedia($input: UploadMediaInput!) {
    uploadMedia(input: $input) {
      id
      filename
      original_name
      media_type
      size
      path
      url
      created_at
      updated_at
    }
  }
`;

const BULK_UPLOAD_MEDIA = gql`
  mutation BulkUploadMedia($input: BulkUploadMediaInput!) {
    bulkUploadMedia(input: $input) {
      id
      filename
      original_name
      media_type
      size
      path
      url
      created_at
      updated_at
    }
  }
`;

const DELETE_MEDIA = gql`
  mutation DeleteMedia($id: ID!) {
    deleteMedia(id: $id)
  }
`;

const CONFIGURE_STORAGE_LOCATION = gql`
  mutation ConfigureStorageLocation($input: ConfigureStorageLocationInput!) {
    configureStorageLocation(input: $input) {
      id
      name
      type
      is_default
      configuration
      created_at
      updated_at
    }
  }
`;

// React Hooks
export const useUploadMedia = () => {
  const [mutate, result] = useMutation(UPLOAD_MEDIA);
  return mutate;
};

export const useBulkUploadMedia = () => {
  const [mutate, result] = useMutation(BULK_UPLOAD_MEDIA);
  return mutate;
};

export const useDeleteMedia = () => {
  const [mutate, result] = useMutation(DELETE_MEDIA);
  return mutate;
};

export const useConfigureStorageLocation = () => {
  const [mutate, result] = useMutation(CONFIGURE_STORAGE_LOCATION);
  return mutate;
};

// Export mutations for direct use
export { UPLOAD_MEDIA, BULK_UPLOAD_MEDIA, DELETE_MEDIA, CONFIGURE_STORAGE_LOCATION };
