const fs = require('fs');
const path = require('path');

// Paths to markdown files
const readmeMdPath = path.join(__dirname, '../../readme.md');
const changelogMdPath = path.join(__dirname, '../../CHANGELOG.md');
const outputTxtPath = path.join(__dirname, '../temp/readme.txt'); // Output in .github/temp

// Read markdown files
const readmeMd = fs.readFileSync(readmeMdPath, 'utf-8');
const changelogMd = fs.readFileSync(changelogMdPath, 'utf-8');

// Helper function to format markdown into WordPress plugin format
function formatLine(line, nextLine) {
    // Remove line with [Download plugin on wordpress.org] and the blank line before it
    if (nextLine && nextLine.includes('[Download plugin on wordpress.org]')) {
        return '';  // Ignore the current (likely blank) line before the removed line
    }

    // Headings
    if (line.startsWith('### ')) {
        return '=== ' + line.substring(4) + ' ===';
    } else if (line.startsWith('## ')) {
        return '== ' + line.substring(3) + ' ==';
    } else if (line.startsWith('# ')) {
        return '=== ' + line.substring(2) + ' ===';
    }

    // Bold text (WordPress readme uses just plain text for this) and remove extra spaces
    line = line.replace(/\*\*(.*?)\*\*/g, '$1').replace(/\s{2,}/g, ' ');

    // Unordered lists
    if (line.startsWith('* ')) {
        return line; // Keep unordered list as is
    }

    // Ordered lists (e.g., "1. ")
    if (/^\d+\.\s/.test(line)) {
        return line; // Keep ordered list as is
    }

    // Blank line
    if (line.trim() === '') {
        return line;
    }

    // For any other line, return unchanged but without extra spaces
    return line.trim();
}

// Function to format the changelog
function formatChangelog(changelogContent) {
    const changelogLines = changelogContent.split('\n');
    const formattedChangelog = changelogLines.map(line => {
        // Match version lines like "### 1.0.0 (10.19.2024)" and convert it to "= 1.0.0 ="
        const versionMatch = line.match(/###\s+(\d+\.\d+\.\d+)/);
        if (versionMatch) {
            return `= ${versionMatch[1]} =`;
        }
        // Format the rest of the changelog like the rest of the markdown
        return formatLine(line, '');
    });
    return formattedChangelog.join('\n');
}

// Split the readme.md into lines
const lines = readmeMd.split('\n');

// Process each line and format it, while removing the empty line before specific removals
const formattedReadmeLines = lines.map((line, i) => {
    const nextLine = lines[i + 1] || '';
    return formatLine(line, nextLine);
});

// Process changelog
const formattedChangelog = formatChangelog(changelogMd);

// Combine readme and changelog
const finalOutput = [...formattedReadmeLines, '', formattedChangelog];

// Write to readme.txt file
fs.writeFileSync(outputTxtPath, finalOutput.join('\n').trim());
console.log(`readme.txt has been generated at ${outputTxtPath}`);
