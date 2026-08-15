# GraphiQL auth examples

Open GraphiQL at `/graphiql`.

Use the endpoint:

- `/graphql`

## 1) Register

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

Copy the returned `token`.

## 2) Login

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

You can also use email in `login`, for example `jane.graphql@example.com`.

## 3) Set headers for authenticated requests

In GraphiQL, open the **Headers** panel and paste:

```json
{
  "Authorization": "Bearer YOUR_TOKEN_HERE"
}
```

## 4) Get current user

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

## 5) Logout (revoke current token)

```graphql
mutation Logout {
  logout
}
```
