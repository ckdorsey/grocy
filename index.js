
const express = require('express');
const app = express();
const path = require('path');
const php = require('php-express')();

// Configure PHP-Express middleware
app.engine('php', php.engine);
app.set('view engine', 'php');
app.set('views', path.join(__dirname, 'views'));

// Serve static files
app.use(express.static('public'));

// Forward PHP requests to the PHP handler
app.all('*.php', php.router);

// Default route
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public/index.php'));
});

const port = 3000;
app.listen(port, '0.0.0.0', () => {
  console.log(`Server running at http://0.0.0.0:${port}`);
});
