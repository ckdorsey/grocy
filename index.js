
const express = require('express');
const app = express();
const path = require('path');
const { exec } = require('child_process');

app.use(express.static('public', {
  index: false,
  setHeaders: (res, path) => {
    if (path.endsWith('.php')) {
      res.setHeader('Content-Type', 'text/html');
    }
  }
}));

app.get('/*', (req, res) => {
  const filePath = req.path === '/' ? '/public/index.php' : req.path;
  
  if (filePath.endsWith('.php')) {
    exec(`php ${process.cwd()}${filePath}`, (error, stdout, stderr) => {
      if (error) {
        console.error(`Error: ${error}`);
        res.status(500).send(error.message);
        return;
      }
      res.send(stdout);
    });
  } else {
    res.sendFile(path.join(__dirname, filePath));
  }
});

const port = 3000;
app.listen(port, '0.0.0.0', () => {
  console.log(`Server running at http://0.0.0.0:${port}`);
});
