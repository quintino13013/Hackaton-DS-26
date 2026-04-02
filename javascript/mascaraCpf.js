const inputCPF = document.getElementById('cpfPessoa');

// Adiciona um event listener para o evento 'input' (ou 'keyup')
inputCPF.addEventListener('input', function (e) {
    let value = e.target.value;

    // Remove caracteres não numéricos do valor atual
    value = value.replace(/\D/g, "");

    // Aplica a máscara de CPF
    if (value.length > 3 && value.length <= 6) {
        value = value.replace(/(\d{3})(\d+)/, "$1.$2");
    } else if (value.length > 6 && value.length <= 9) {
        value = value.replace(/(\d{3})(\d{3})(\d+)/, "$1.$2.$3");
    } else if (value.length > 9) {
        value = value.replace(/(\d{3})(\d{3})(\d{3})(\d+)/, "$1.$2.$3-$4");
    }

    // Limita o valor final para garantir o maxlength de 14 caracteres (incluindo máscara)
    if (value.length > 14) {
      value = value.substring(0, 14);
    }

    // Atualiza o valor do input com a máscara aplicada
    e.target.value = value;
});