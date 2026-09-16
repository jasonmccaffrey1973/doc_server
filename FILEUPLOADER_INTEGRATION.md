File Uploader GraphQL integration

This repository (backend) worktree contains the GraphQL server exposing uploadMedia and bulkUploadMedia mutations (Upload scalar).

Frontend changes were made in the separate repository jasonmccaffrey1973/lms-front on branch jasonmccaffrey1973-login-integration. Summary of frontend edits:

- useFileUploader.ts: Added a default GraphQL multipart upload handler that calls the uploadMedia mutation via the GraphQL multipart request spec. Reports progress, supports AbortController cancellation, and defaults to /graphql.
- README update: Documented new graphqlEndpoint prop for FileUploader.

Notes for reviewers:
- The frontend repo contains the implementation and should be reviewed there as well. If desired, a PR can be opened in that repository to merge the frontend changes.
- Backend GraphQL already supports the Upload scalar and uploadMedia mutations. Ensure Lighthouse multipart handling and storage locations are configured for uploads.

How to test locally:
1. Start backend server and ensure /graphql is reachable and authenticated.
2. In a separate workspace, checkout jasonmccaffrey1973/lms-front and switch to branch jasonmccaffrey1973-login-integration, run yarn install, yarn dev.
3. Use the FileUploader component (autoUpload or Upload All) to send files and verify media is created via GraphQL queries.

Co-authored-by: Copilot App <223556219+Copilot@users.noreply.github.com>
