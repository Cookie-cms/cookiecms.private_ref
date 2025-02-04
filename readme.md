[Original code](https://github.com/Cookie-cms/cookie_cms)<br>
~~[Old auth slider](https://github.com/AsmrProg-YT/Modern-Login)<br>~~
~~[skin viewer](https://github.com/bs-community/skinview3d)<br>~~ => moved to frontend
[Api skins gravit launcher](https://gravitlauncher.com/other/#%D0%BC%D0%B5%D1%82%D0%BE%D0%B4-json)<br>
~~[template engine](https://github.com/Cookie-cms/engine/tree/main/TemplateEngine)<br>~~

[wiki](https://wiki-cookiecms.coffeedev.dev/)

# Cookie CMS API Server

Welcome to the **Cookie CMS API Server**! 🎉 This project is designed to provide a powerful and secure backend for user authentication, account management, and seamless integration with external services like Discord. Below you will find an overview of the project, its key features, and how to set it up using Docker.

## Project Overview

The Cookie CMS API Server is built to support a variety of functionalities that enhance user experience and security. It allows users to register, log in, manage their accounts, and interact with the system efficiently. The server is designed with scalability in mind, making it suitable for various applications.

## Key Features

- **User Authentication** 🔐: 
  - Facilitates user registration, login, email confirmation, and logout processes.
  - Utilizes JSON Web Tokens (JWT) for secure session management.

- **Account Management** 🛠️: 
  - Users can update their account details such as usernames and passwords.
  - Supports email verification to enhance security.

- **Discord Integration** 🎮: 
  - Users can connect their Discord accounts for enhanced functionality.
  - Manage Discord settings directly within the application.

- **Admin Capabilities** 👨‍💼: 
  - Administrators can manage users effectively through dedicated endpoints.
  - Features include fetching user lists, updating user roles, and sending communications.

- **Service Endpoints** ⚙️: 
  - Provides additional functionalities such as finding user accounts based on Discord IDs or player names.

## Security Considerations

The API implements robust security measures including:
- JWT authentication for protected routes.
- Email verification processes to maintain account integrity.

## Getting Started

To set up the Cookie CMS API Server via Docker, follow these simple steps:

1. Clone the repository:
   ```sh
   git clone https://github.com/Cookie-cms/cookie_cms.git
   cd cookie_cms
   ```

2. Run the following command to start the server:
   ```sh
   docker compose up -d
   ```

This command will initialize the Docker containers necessary for running the API server.

## Documentation

For detailed API documentation, including endpoint descriptions and usage examples, please refer to the [Wiki](https://wiki-cookiecms.coffeedev.dev/).

## Conclusion

The Cookie CMS API Server is a comprehensive solution tailored for modern applications requiring user management and integration capabilities. With its robust features and security measures, it is positioned to support a wide range of use cases effectively. Explore the project further on our GitHub repository! 🚀
