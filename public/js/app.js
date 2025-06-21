const videoPreview = document.getElementById('video-preview');
const statusDiv = document.getElementById('status');
const startBtn = document.getElementById('start-scan');
const form = document.getElementById('info-form');
const productCodeInput = document.getElementById('product-code');
const expiryDateInput = document.getElementById('expiry-date');
const lotNumberInput = document.getElementById('lot-number');
const instCodeInput = document.getElementById('institution-code');
const hospitalInput = document.getElementById('hospital');
const rescanBtn = document.getElementById('rescan');
let codeReader;

function resetUI() {
  form.style.display = 'none';
  startBtn.style.display = 'block';
  videoPreview.style.display = 'none';
  statusDiv.textContent = '';
  productCodeInput.value = '';
  expiryDateInput.value = '';
  lotNumberInput.value = '';
  instCodeInput.value = '';
  hospitalInput.value = '';
}

function parseGS1(text) {
  const cleaned = text.replace(/[()]/g, '').replace(/\u001d/g, '');
  const m01 = cleaned.match(/01(\d{14})/);
  const m17 = cleaned.match(/17(\d{6})/);
  let lot = '';
  const m10 = cleaned.match(/10([^\u001d]+)/);
  if (m10) lot = m10[1];
  return {
    productCode: m01 ? m01[1] : '',
    expiryDate: m17 ? m17[1] : '',
    lotNumber: lot
  };
}

startBtn.addEventListener('click', () => {
  startBtn.style.display = 'none';
  statusDiv.textContent = 'カメラ起動中…';
  videoPreview.style.display = 'block';
  const hints = new Map();
  hints.set(ZXing.DecodeHintType.POSSIBLE_FORMATS, [
    ZXing.BarcodeFormat.RSS_EXPANDED,
    ZXing.BarcodeFormat.RSS_14,
    ZXing.BarcodeFormat.CODE_128,
    ZXing.BarcodeFormat.EAN_13,
    ZXing.BarcodeFormat.UPC_A
  ]);
  codeReader = new ZXing.BrowserMultiFormatReader(hints);
  codeReader.listVideoInputDevices().then(devices => {
    if (!devices.length) throw new Error('カメラデバイスが見つかりません');
    const deviceId = devices[devices.length - 1].deviceId;
    codeReader.decodeFromVideoDevice(deviceId, videoPreview, (result, err) => {
      if (result) {
        codeReader.reset();
        videoPreview.style.display = 'none';
        const text = result.getText();
        const fields = parseGS1(text);
        productCodeInput.value = fields.productCode;
        expiryDateInput.value = fields.expiryDate;
        lotNumberInput.value = fields.lotNumber;
        form.style.display = 'block';
        statusDiv.textContent = 'コードを取得しました。必要事項を入力してください。';
        instCodeInput.focus();
      }
      if (err && !(err instanceof ZXing.NotFoundException)) {
        console.warn(err);
      }
    });
  }).catch(err => {
    statusDiv.textContent = 'カメラ起動に失敗しました: ' + err;
    startBtn.style.display = 'block';
    videoPreview.style.display = 'none';
  });
});

rescanBtn.addEventListener('click', () => {
  if (codeReader) codeReader.reset();
  resetUI();
});

form.addEventListener('submit', e => {
  e.preventDefault();
  const formData = new FormData(form);
  fetch('form.php', { method: 'POST', body: formData })
    .then(r => r.text())
    .then(t => { alert(t); resetUI(); })
    .catch(err => alert(err));
});

resetUI();
