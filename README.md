# How to Add an Auto-Update System to Any WordPress Plugin

This guide explains how to integrate the GitHub-based auto-update system into any WordPress plugin so you can easily push updates to your users.

---

## Step 1: Install the `plugin-update-checker` Library
First, you need to include the update checker library inside your new plugin's folder. 

1. Open your terminal/command prompt.
2. Navigate to your new plugin's folder (e.g., `wp-content/plugins/your-new-plugin/`).
3. Run the following command to clone the library directly into your plugin:
   ```bash
   git clone https://github.com/YahnisElsts/plugin-update-checker.git plugin-update-checker
   ```
4. Remove the `.git` folder from the library so it doesn't interfere with your plugin's Git tracking:
   ```bash
   Remove-Item -Recurse -Force plugin-update-checker\.git
   ```
   *(For Mac/Linux use: `rm -rf plugin-update-checker/.git`)*

---

## Step 2: Add the Initialization Code
Open your plugin's main PHP file (e.g., `your-new-plugin.php`) and add the following code right below the plugin headers and security check:

```php
// Include the update checker library
require_once plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

// Initialize the update checker
$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/your-username/your-repo-name/', // <-- Change this to your GitHub repo URL
	__FILE__,
	'your-new-plugin' // <-- Change this to your plugin's folder name (slug)
);

// Set the branch that contains the stable release
$myUpdateChecker->setBranch( 'main' );

// OPTIONAL: If your repo is PRIVATE, uncomment the line below and add your token.
// IMPORTANT: Never push the actual token to GitHub! Add it only on the client's site.
// $myUpdateChecker->setAuthentication('your-github-access-token');
```

---

## Step 3: Add the GitHub Actions Workflow (Auto Zip Creator)
To make sure GitHub automatically creates a clean `.zip` file for your users to download when you release an update, you need to add a GitHub Actions workflow.

1. Inside your plugin folder, create these folders: `.github/workflows/`
2. Inside the `workflows` folder, create a file named `release.yml`.
3. Paste the following code into `release.yml`:

```yaml
name: Create Release

on:
  push:
    tags:
      - 'v*'

jobs:
  release:
    runs-on: ubuntu-latest
    permissions:
      contents: write
    steps:
      - name: Checkout repository
        uses: actions/checkout@v3

      - name: Create Plugin ZIP
        run: |
          # CHANGE "your-new-plugin" to your actual plugin slug
          mkdir your-new-plugin
          rsync -rr --exclude='.git/' --exclude='.github/' --exclude='your-new-plugin' ./ your-new-plugin/
          zip -r your-new-plugin.zip your-new-plugin/

      - name: Create GitHub Release
        uses: softprops/action-gh-release@v1
        with:
          files: your-new-plugin.zip # <-- Must match the zip name above
          generate_release_notes: true
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

---

## Step 4: How to Release a New Update
Whenever you want to release a new version to your users, follow these exact steps:

1. **Update the Version:** Open your main plugin PHP file and change the `Version:` number (e.g., from `1.0.0` to `1.0.1`).
2. **Commit the Changes:**
   ```bash
   git add .
   git commit -m "Update to version 1.0.1"
   ```
3. **Create a Tag:** (The tag must start with `v` if you used the workflow above)
   ```bash
   git tag -a v_1.0.1 -m "Version 1.0.1"
   ```
4. **Push Everything:**
   ```bash
   git push origin main --tags
   ```

**That's it!** 
GitHub Actions will automatically catch the new tag, create a fresh `.zip` file, and publish the release. Within 12 hours, any WordPress site using your plugin will automatically show an "Update Available" notification.
