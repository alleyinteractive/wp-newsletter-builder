# Blocks Directory

Custom blocks in this directory can be created by running the `create-block` script. For understanding how blocks are architected, built, and enqueued refer to the [Block Editor Handbook | File Structure of a Block](https://developer.wordpress.org/block-editor/getting-started/fundamentals/file-structure-of-a-block/).

## Scaffold a block with `create-block`

1. In the root directory run `npm run create-block`
2. Choose TypeScript.
3. Follow the prompts to create a custom block, selecting "dynamic" as the block type.

[Dynamic blocks](https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/creating-dynamic-blocks/) have a `render.php` file for server side output on the front end.

The `create-block` script will create the block files in the block directory using the `slug` field entered from the prompts when scaffolding the block.

The script uses the [@alleyinteractive/create-block](https://www.npmjs.com/package/@alleyinteractive/create-block) script for scaffolding block files. See the `create-block` script in `package.json`.

The following files will be generated:

```
blocks/
└───block-slug
    │   block.json
    │   edit.jsx
    |   index.scss
    |   index.js
    |   index.php
    |   styles.scss
    |   render.php
```

The `index.php` contains the PHP block registration and will be autoloaded with the `load_scripts()` function once the block has been built by running `npm run build`.

Block attributes should be defined in the `block.json` file. [Learn more about block.json in the block editor handbook.](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/)

Running `npm run build` will compile the TypeScript and copy the PHP files to a directory in the `build` folder using `alley-build`. The blocks will be enqueued via `block.json` after block registration. The block `index.php` file will be read by the `load_scripts()` function found in the `src/assets.php` file.
