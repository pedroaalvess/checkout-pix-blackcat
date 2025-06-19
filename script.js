document.addEventListener('DOMContentLoaded', () => {
  const unitPrice = 10.0; // Unit price in BRL
  const quantityInput = document.getElementById('quantity');
  const totalPriceDisplay = document.getElementById('totalPrice');
  const form = document.getElementById('checkout-form');
  const paymentInfoSection = document.getElementById('paymentInfo');
  const qrCodeImage = document.getElementById('qrCodeImage');
  const copyPasteCode = document.getElementById('copyPasteCode');
  const paymentValue = document.getElementById('paymentValue');
  const paymentMessage = document.getElementById('paymentMessage');
  const generatePixBtn = document.getElementById('generatePixBtn');

  function formatBRL(value) {
    return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  }

  function updateTotal() {
    const quantity = parseInt(quantityInput.value, 10) || 1;
    const total = unitPrice * quantity;
    totalPriceDisplay.textContent = formatBRL(total);
  }

  quantityInput.addEventListener('input', updateTotal);

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    generatePixBtn.disabled = true;
    generatePixBtn.textContent = 'Gerando...';

    const formData = new FormData(form);
    const data = {
      fullName: formData.get('fullName').trim(),
      cpf: formData.get('cpf').trim(),
      email: formData.get('email').trim(),
      phone: formData.get('phone').trim(),
      quantity: parseInt(formData.get('quantity'), 10) || 1,
      unitPrice: unitPrice,
    };

    // Basic validation
    if (!data.fullName || !data.cpf || !data.email || !data.phone || data.quantity < 1) {
      alert('Por favor, preencha todos os campos corretamente.');
      generatePixBtn.disabled = false;
      generatePixBtn.textContent = 'Gerar Pix';
      return;
    }

    try {
      const response = await fetch('pix.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
      });

      if (!response.ok) {
        throw new Error('Erro na requisição ao servidor.');
      }

      const result = await response.json();

      if (result.error) {
        alert('Erro: ' + result.error);
        generatePixBtn.disabled = false;
        generatePixBtn.textContent = 'Gerar Pix';
        return;
      }

      // Show payment info
      qrCodeImage.src = result.qrCodeUrl;
      copyPasteCode.value = result.copyPasteCode;
      paymentValue.textContent = 'Valor: ' + formatBRL(result.amount / 100);
      paymentMessage.textContent = 'Aguardando pagamento...';
      paymentInfoSection.classList.remove('hidden');
    } catch (error) {
      alert('Erro ao gerar Pix: ' + error.message);
    } finally {
      generatePixBtn.disabled = false;
      generatePixBtn.textContent = 'Gerar Pix';
    }
  });

  updateTotal();
});
