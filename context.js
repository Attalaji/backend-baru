/**
 * Usage: node context.js
 * Output: project_context.txt
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Helper for ES Modules to get current directory (since __dirname doesn't exist)
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Configuration: Ignore these folders and files common in PHP/Backend projects
const IGNORE_DIRS = new Set([
  'node_modules', 
  'vendor',       // Crucial for PHP projects
  '.git', 
  '.vscode', 
  'storage',      
  'bootstrap/cache', 
  'tests/_output',
  'dist',
  'build',
  'coverage',
  'logs'
]);

const IGNORE_FILES = new Set([
  'package-lock.json', 
  'composer.lock', 
  'pnpm-lock.yaml',
  '.DS_Store', 
  '.env', 
  '.env.local',
  '.env.example', 
  'context.js',         // Ignores itself
  'context.cjs',        
  'project_context.txt' // Ignores the output
]);

// Configuration: Include PHP and common backend configuration files
const ALLOWED_EXTS = new Set([
  '.php', 
  '.sql', 
  '.json', 
  '.yaml', 
  '.yml', 
  '.md', 
  '.htaccess', 
  '.conf',
  '.js',
  '.ts'
]);

const OUTPUT_FILE = 'project_context.txt';

function getFiles(dir) {
  let results = [];
  let list;
  
  try {
    list = fs.readdirSync(dir);
  } catch (err) {
    return results;
  }
  
  for (const file of list) {
    const filePath = path.join(dir, file);
    let stat;
    
    try {
      stat = fs.statSync(filePath);
    } catch (e) {
      continue; 
    }
    
    const fileName = path.basename(file);
    
    if (stat.isDirectory()) {
      if (!IGNORE_DIRS.has(fileName)) {
        results = results.concat(getFiles(filePath));
      }
    } else {
      const ext = path.extname(file).toLowerCase();
      // Logic: If it's not in the ignore list AND (extension is allowed OR it's a special file like .htaccess)
      if (!IGNORE_FILES.has(fileName) && (ALLOWED_EXTS.has(ext) || ALLOWED_EXTS.has(fileName))) {
        results.push(filePath);
      }
    }
  }
  return results;
}

try {
  const rootDir = process.cwd();
  const files = getFiles(rootDir);
  
  let output = `Backend Project Root: ${path.basename(rootDir)}\n`;
  output += `Generated on: ${new Date().toLocaleString()}\n\n`;
  
  // 1. Add File Structure
  output += "--- PROJECT STRUCTURE ---\n";
  files.forEach(file => {
    output += `${path.relative(rootDir, file)}\n`;
  });
  
  // 2. Add File Contents
  output += "\n--- FILE CONTENTS ---\n";
  files.forEach(file => {
    const relativePath = path.relative(rootDir, file);
    
    try {
      const content = fs.readFileSync(file, 'utf8');
      output += `\n================================================================================\n`;
      output += `File: ${relativePath}\n`;
      output += `================================================================================\n`;
      output += content + "\n";
    } catch (readError) {
      output += `\n[Error reading file: ${relativePath}]\n`;
    }
  });

  fs.writeFileSync(OUTPUT_FILE, output);
  console.log(`\x1b[32m%s\x1b[0m`, `✅ Success! Context generated in '${OUTPUT_FILE}'.`);
  console.log(`Processed ${files.length} files.`);
} catch (error) {
  console.error("Error generating context:", error);
}