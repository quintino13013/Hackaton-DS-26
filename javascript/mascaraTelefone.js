    function mascaraTelefone(input) {
  let value = input.value;
  
  // Remove tudo que não é dígito
  value = value.replace(/\D/g, "");
  
  // Limita a 11 caracteres (DDD + 9 dígitos)
  value = value.substring(0, 11);
  
  // Formata: (DD) 99999-9999
  value = value.replace(/^(\d{2})(\d)/g, "($1) $2");
  value = value.replace(/(\d)(\d{4})$/, "$1-$2");
  
  input.value = value;
}