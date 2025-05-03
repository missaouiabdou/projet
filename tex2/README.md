# Real-Time Chat Application

A real-time chat application built with PHP, WebSockets (Ratchet), and MySQL.

## Features

- Real-time messaging using WebSockets
- No login required (nickname-based)
- Message persistence in MySQL database
- Modern and responsive UI
- Auto-scrolling to latest messages
- Message timestamps

## Prerequisites

- PHP 7.4 or higher
- MySQL
- Composer
- WebSocket-enabled web browser

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```
3. Import the database schema:
   ```bash
   mysql -u root -p < database.sql
   ```
4. Configure database credentials in `src/Chat.php` if needed

## Running the Application

1. Start the WebSocket server:
   ```bash
   php server.php
   ```
2. Open `index.php` in your web browser
3. Enter a nickname when prompted
4. Start chatting!

## Project Structure

- `index.php` - Frontend interface
- `server.php` - WebSocket server entry point
- `src/Chat.php` - WebSocket server implementation
- `database.sql` - Database schema
- `composer.json` - Dependencies configuration

## Security Notes

- This is a basic implementation and should not be used in production without additional security measures
- Consider implementing rate limiting and input validation
- Add proper authentication for production use

## License

MIT 