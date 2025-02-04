document.addEventListener('livewire:initialized', () => {
    Livewire.on('print-sales-transaction-receipt', (event) => {
        console.log(event);
    });
 });