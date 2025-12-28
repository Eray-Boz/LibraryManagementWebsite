<?php
$host = 'localhost';
$user = 'root';
$pass = '';


$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected to MySQL server.<br>";


$sql = "CREATE DATABASE IF NOT EXISTS library_db";
if ($conn->query($sql) === TRUE) {
    echo "Database 'library_db' created or already exists.<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}


$conn->select_db("library_db");


$sqlUsers = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sqlUsers) === TRUE) {
    echo "Table 'users' created or already exists.<br>";
} else {
    echo "Error creating table users: " . $conn->error . "<br>";
}


$sqlBooks = "CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20),
    published_year INT,
    genre VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sqlBooks) === TRUE) {
    echo "Table 'books' created or already exists.<br>";
} else {
    echo "Error creating table books: " . $conn->error . "<br>";
}


$adminUser = 'admin';
$adminPass = 'admin123'; 

$checkUser = "SELECT * FROM users WHERE username = '$adminUser'";
$result = $conn->query($checkUser);

if ($result->num_rows == 0) {
    
    $safeUser = $conn->real_escape_string($adminUser);
    $safePass = $conn->real_escape_string($adminPass);
    
    $sqlInsert = "INSERT INTO users (username, password) VALUES ('$safeUser', '$safePass')";
    if ($conn->query($sqlInsert) === TRUE) {
        echo "Admin user created (User: admin, Pass: admin123).<br>";
    } else {
        echo "Error creating admin user: " . $conn->error . "<br>";
    }
} else {
    
    $safePass = $conn->real_escape_string($adminPass);
    $sqlUpdate = "UPDATE users SET password = '$safePass' WHERE username = '$adminUser'";
    $conn->query($sqlUpdate);
    echo "Admin user checked/updated.<br>";
}


$checkBooks = "SELECT COUNT(*) as count FROM books";
$result = $conn->query($checkBooks);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $books = [
    ['The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 1925, 'Classic'],
    ['To Kill a Mockingbird', 'Harper Lee', '9780061120084', 1960, 'Classic'],
    ['1984', 'George Orwell', '9780451524935', 1949, 'Dystopian'],
    ['The Catcher in the Rye', 'J.D. Salinger', '9780316769488', 1951, 'Classic'],
    ['Pride and Prejudice', 'Jane Austen', '9780141439518', 1813, 'Romance'],
    ['Brave New World', 'Aldous Huxley', '9780060850524', 1932, 'Dystopian'],
    ['The Lord of the Rings', 'J.R.R. Tolkien', '9780544003415', 1954, 'Fantasy'],
    ['The Hobbit', 'J.R.R. Tolkien', '9780547928227', 1937, 'Fantasy'],
    ['Fahrenheit 451', 'Ray Bradbury', '9781451673319', 1953, 'Dystopian'],
    ['Moby-Dick', 'Herman Melville', '9781503280786', 1851, 'Classic'],
    ['Crime and Punishment', 'Fyodor Dostoevsky', '9780140449136', 1866, 'Classic'],
    ['The Alchemist', 'Paulo Coelho', '9780062315007', 1988, 'Fiction'],
    ['Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', '9780590353427', 1997, 'Fantasy'],
    ['The Da Vinci Code', 'Dan Brown', '9780307474278', 2003, 'Thriller'],
    ['The Hunger Games', 'Suzanne Collins', '9780439023528', 2008, 'Dystopian']
];
    
    $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, published_year, genre) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($books as $book) {
        
        $stmt->bind_param("sssis", $book[0], $book[1], $book[2], $book[3], $book[4]);
        $stmt->execute();
    }
    echo "Seed data for books inserted.<br>";
    $stmt->close();
} else {
    echo "Books table already has data.<br>";
}

$sqlMembers = "CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sqlMembers) === TRUE) {
    echo "Table 'members' created or already exists.<br>";
} else {
    echo "Error creating table members: " . $conn->error . "<br>";
}

$sqlLoans = "CREATE TABLE IF NOT EXISTS loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    member_id INT NOT NULL,
    borrow_date DATETIME NOT NULL,
    return_date DATETIME,
    status ENUM('borrowed', 'returned') DEFAULT 'borrowed',
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
)";
if ($conn->query($sqlLoans) === TRUE) {
    echo "Table 'loans' created or already exists.<br>";
} else {
    echo "Error creating table loans: " . $conn->error . "<br>";
}

$checkMembers = "SELECT COUNT(*) as count FROM members";
$result = $conn->query($checkMembers);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $members = [
    ['John Doe', 'john@example.com', '555-0101'],
    ['Jane Smith', 'jane@example.com', '555-0102'],
    ['Alice Jones', 'alice@example.com', '555-0103'],
    ['Michael Brown', 'michael@example.com', '555-0104'],
    ['Emily Davis', 'emily@example.com', '555-0105'],
    ['Daniel Wilson', 'daniel@example.com', '555-0106'],
    ['Sophia Taylor', 'sophia@example.com', '555-0107'],
    ['James Anderson', 'james@example.com', '555-0108'],
    ['Olivia Martinez', 'olivia@example.com', '555-0109'],
    ['William Thompson', 'william@example.com', '555-0110']
];
    
    $stmt = $conn->prepare("INSERT INTO members (full_name, email, phone) VALUES (?, ?, ?)");
    foreach ($members as $member) {
        $stmt->bind_param("sss", $member[0], $member[1], $member[2]);
        $stmt->execute();
    }
    echo "Seed data for members inserted.<br>";
    $stmt->close();
}

$conn->close();
?>
