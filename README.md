# creamPuff

This repository provides a simple web page that scans GS1 DataBar barcodes and
prepares an email containing the decoded information. The sample uses the
[ZXing](https://github.com/zxing-js/library) library running in the browser.

## Features

- Starts the device camera and attempts to decode GS1 DataBar or CODE-128 barcodes.
- Extracts the product code (AI=01), expiration date (AI=17) and lot number (AI=10).
- Allows entering the institution code and hospital name and generates a mailto
  link to share the information.

> **Note**: ZXing's browser library does not fully support GS1 DataBar Composite
> symbols. The sample works best with plain DataBar or CODE-128 symbols.

## Usage

Open `index.html` in a modern web browser and allow camera access. After scanning
an eligible barcode, fill in the additional fields and click the button to open
your mail client with a prepared message.
