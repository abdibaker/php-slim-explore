import fs from 'fs';
import { exec } from 'child_process';

export async function buildApp() {
  const buildDirectory = 'build';

  try {
    // Create build directory if it doesn't exist
    if (!fs.existsSync(buildDirectory)) {
      fs.mkdirSync(buildDirectory, { recursive: true });
    }

    // Copy files and directories to the build directory
    fs.copyFileSync('composer.json', `${buildDirectory}/composer.json`);
    fs.copyFileSync('composer.lock', `${buildDirectory}/composer.lock`);
    fs.copyFileSync('.htaccess', `${buildDirectory}/.htaccess`);
    copyDirectory('./src', `${buildDirectory}/src`);
    copyDirectory('./public', `${buildDirectory}/public`);

    // Change to the build directory
    process.chdir(buildDirectory);

    // Run composer install command
    exec(
      'composer install --no-dev --optimize-autoloader',
      (error, stdout, stderr) => {
        if (error) {
          console.error(
            `Error running composer install: ${error.message}`
          );
          return;
        }
        console.log(stdout);
        console.error(stderr);

        // Delete composer files
        fs.unlinkSync('composer.json');
        fs.unlinkSync('composer.lock');
        fs.unlinkSync(`public/swagger/local.html`);

        // Change back to the original directory
        process.chdir('..');
        console.log('Build completed.');
      }
    );
  } catch (error) {
    console.error(
      `Error building the application: ${error.message}`
    );
  }
}

function copyDirectory(source, destination) {
  if (!fs.existsSync(destination)) {
    fs.mkdirSync(destination);
  }

  const files = fs.readdirSync(source);

  for (const file of files) {
    const sourcePath = `${source}/${file}`;
    const destinationPath = `${destination}/${file}`;

    if (fs.lstatSync(sourcePath).isDirectory()) {
      copyDirectory(sourcePath, destinationPath);
    } else {
      fs.copyFileSync(sourcePath, destinationPath);
    }
  }
}

// Call the buildApp function to start the build process
buildApp();
