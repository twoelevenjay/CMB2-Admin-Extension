Thank you to WebDevStudios for this awesome CONTRIBUTING.md file ;-)

# Contributing to CMB2 Admin Extension

Thank you for your interest in contributing back to CMB2 Admin Extension. Please help us review your issues and/or merge your pull requests by following the below guidelines.

#### NOTE: The issues section is for bug reports and feature requests only.
_Support is not offered for this library, and the likelihood that the maintainers will respond is very low. If you need help, please use [stackoverflow](http://stackoverflow.com/search?q=cmb), or the [wordpress.org plugin forums](http://wordpress.org/support/plugin/cmb2)._

---

## Before reporting a bug
1. Search [issues](https://github.com/twoelevenjay/CMB2-Admin-Extension/issues) to see if the issue has been previously reported.

---

## How to report a bug
1. Specify the version number for both WordPress and CMB2 Admin Extension.
2. Describe the problem in detail. Explain what happened, and what you expected would happen.
3. Provide a small test-case and a link to a [gist](https://gist.github.com/) containing your entire metabox registration code.
4. If helpful, include a screenshot. Annotate the screenshot for clarity.

---

## How to contribute to CMB2 Admin Extension
All contributions welcome. If you would like to submit a pull request, please follow the steps below.

1. Make sure you have a GitHub account.
2. Fork the repository on GitHub.
3. Make changes to your clone of the repository.
   1. Please follow the [WordPress code standards](https://make.wordpress.org/core/handbook/coding-standards).
   2. If possible, and if applicable, please also add/update unit tests for your changes.
   3. Please add documentation to any new functions, methods, actions, and filters.
   4. When committing, reference your issue (if present) and include a note about the fix.
4. [Submit a pull request](https://help.github.com/articles/creating-a-pull-request/).

**Note:** You may gain more ground and avoid unnecessary effort if you first open an issue with the proposed changes, but this step is not necessary.

---

## Using npm-watch for SASS and JS Minification

We have integrated a development environment for watching and compiling SASS and minifying JavaScript files. To set it up and use it locally, follow these steps:

1. Make sure Node.js and npm are installed on your system. If not, download and install Node.js from [https://nodejs.org](https://nodejs.org).
   
2. Install the necessary npm packages:
   ```bash
   npm install
   ```

3. To automatically watch and compile changes, run:
   ```bash
   npm run watch
   ```

This will:
- Watch for changes in your `.scss` files and compile them into CSS.
- Watch for changes in your non-minified `.js` files and minify them on save.

The setup is managed via `npm-watch`, and the `watch` command is configured in the `package.json` file.

---

## Code Quality Tools

### PHP Mess Detector (phpmd)
We use **PHP Mess Detector (phpmd)** to detect potential issues in the code, such as code smells, possible bugs, unused code, and overly complex expressions. The `phpmd.xml` file contains the configuration for this tool, specifying the ruleset that checks for maintainability and code quality issues.

To run PHP Mess Detector:
```bash
phpmd . text phpmd.xml
```

This will analyze the entire repository using the rules defined in `phpmd.xml` and output the results in plain text.

### PHP Code Sniffer (phpcs)
**PHP Code Sniffer (phpcs)** helps ensure the code follows PHP coding standards, such as WordPress's PHP coding standards. The `.phpcs.xml.dist` file contains the coding standard rules used for this project. This ensures that all contributions adhere to a consistent coding style.

To run PHP Code Sniffer:
```bash
phpcs --standard=.phpcs.xml.dist .
```

This will check the repository for code standard violations.

### .jscsrc and .jshintignore
- **.jscsrc** is the configuration file for **JSCS**, a JavaScript code style checker. It enforces specific code style rules to ensure a consistent style throughout the project. It contains rules like spacing, indentation, and naming conventions.
  
  To run JSCS:
  ```bash
  jscs .
  ```

  This will check the JavaScript files in the repository for any code style issues based on the rules specified in `.jscsrc`.

- **.jshintignore** is used by **JSHint**, a tool that helps detect errors and potential problems in JavaScript code. The `.jshintignore` file specifies which files or directories should be excluded from the linting process (e.g., `node_modules` or minified files).

  To run JSHint:
  ```bash
  jshint .
  ```

  This will lint the JavaScript files in the repository, ignoring any paths specified in `.jshintignore`.

---

By following these instructions, you can ensure your contributions meet the quality and style guidelines for the CMB2 Admin Extension project.