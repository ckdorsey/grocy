
const express = require('express');
const { exec } = require('child_process');
const path = require('path');
const app = express();
const port = 3000;

// Serve static files from public directory
app.use(express.static('public'));

// Handle PHP files
app.all('*.php', (req, res) => {
  const phpFile = path.join(__dirname, 'public', req.path);
  exec(`php ${phpFile}`, (error, stdout, stderr) => {
    if (error) {
      console.error(`Error: ${error}`);
      res.status(500).send(error.message);
      return;
    }
    res.send(stdout);
  });
});

// Default route to index.php
app.get('/', (req, res) => {
  exec(`php ${path.join(__dirname, 'public/index.php')}`, (error, stdout, stderr) => {
    if (error) {
      console.error(`Error: ${error}`);
      res.status(500).send(error.message);
      return;
    }
    res.send(stdout);
  });
});

app.listen(port, '0.0.0.0', () => {
  console.log(`Server running at http://0.0.0.0:${port}`);
});
