const express = require('express');
const { spawn } = require('child_process');
const app = express();
const port = 3000;

// Serve static files
app.use('/public', express.static('public'));
app.use('/viewjs', express.static('public/viewjs'));
app.use('/css', express.static('public/css'));
app.use('/packages', express.static('public/packages'));

// Start PHP built-in server
const php = spawn('php', ['-S', '127.0.0.1:8000', '-t', 'public']);

php.stdout.on('data', (data) => {
  console.log(`PHP output: ${data}`);
});

php.stderr.on('data', (data) => {
  console.error(`PHP error: ${data}`);
});

// Proxy all requests to PHP server
app.use('/', (req, res) => {
  const url = `http://127.0.0.1:8000${req.url}`;
  req.pipe(require('http').request(url, (resp) => {
    resp.pipe(res);
  }));
});

app.listen(port, '0.0.0.0', () => {
  console.log(`Server running at http://0.0.0.0:${port}`);
});