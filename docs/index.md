## Triangle Skeleton Demo

# Requirements
- PHP 8.1
- Composer
- Symfony CLI
- Docker
- Docker Compose

# User stories

## Permissions

All users behaviour are based on their permissions. 
Permissions are defined by the role of the user.

### Roles

- ROLE_ADMIN - has all permissions
- ROLE_USER - has limited permissions

### Permissions

- Create new user
- Delete user (activate)

## User scenario

1. User logs in

User see two buttons with his choices: "Enter" and "Cancel".

When user clicks on the "Enter" button he goes to the page "Login".

To log in user should provide his email and password.

When user clicks on the "Cancel" button he goes to the page "Register".

# how to back to the main page?

Acceptance criteria:
- user can log in
- user can register
- user can see the main page


