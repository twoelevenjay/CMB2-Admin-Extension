const fs = require('fs');
const path = require('path');

const readmePath = path.join(__dirname, '../../readme.md');
const changelogPath = path.join(__dirname, '../../CHANGELOG.md');
const outputPath = path.join(__dirname, '../temp/readme.txt');

// Function to detect and remove badge lines
function shouldRemoveLine(line) {
  // Check if the line is a badge or related line (e.g., badges inside [])
  return line.startsWith('[!') || line.includes('![Build Status]') || line.includes('badges/');
}

function formatLine(line) {
  if (line.startsWith('### ')) {
    return `= ${line.replace('### ', '').trim()} =\n`;
  }
  if (line.startsWith('## ')) {
    return `== ${line.replace('## ', '').trim()} ==\n`;
  }
  if (line.startsWith('# ')) {
    return `=== ${line.replace('# ', '').trim()} ===\n`;
  }
  // Replace **bold** and remove extra spaces
  return line.replace(/\*\*/g, '').replace(/\s+/g, ' ') + '\n';
}

// Check if the output path (readme.txt) exists, and if not, create it
function ensureFileExists(filePath) {
  const dir = path.dirname(filePath);
  if (!fs.existsSync(dir)) {
    fs.mkdirSync(dir, { recursive: true }); // Create directories if needed
  }
  if (!fs.existsSync(filePath)) {
    fs.writeFileSync(filePath, '', 'utf8'); // Create the file if it doesn't exist
  }
}

// Read the files and filter the lines
function processFiles() {
  const readmeMd = fs.readFileSync(readmePath, 'utf8').split('\n');
  const changelogMd = fs.readFileSync(changelogPath, 'utf8').split('\n');
  const output = [];

  // Process readme.md file
  readmeMd.forEach(line => {
    if (!shouldRemoveLine(line)) {
      output.push(formatLine(line));
    }
  });

  // Process changelog.md and format accordingly
  changelogMd.forEach(line => {
    if (line.startsWith('### ')) {
      output.push(`= ${line.replace('### ', '').trim()} =\n`);
    } else {
      output.push(line + '\n');
    }
  });

  // Ensure the output file exists and write the content
  ensureFileExists(outputPath);
  fs.writeFileSync(outputPath, output.join(''), 'utf8');
  console.log(`readme.txt generated at ${outputPath}`);
}

processFiles();
