async function applyDiscount() {
    try {
        const code = this.discountCode;
        const response = await fetch('/api/discount-codes/validate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ code })
        });

        const data = await response.json();

        if (!response.ok) {
            this.discountError = data.message || 'An error occurred while applying the discount code';
            return;
        }

        this.discountPercentage = data.discount_percentage;
        this.discountError = '';
        this.calculateTotal(); // Make sure you have this method to recalculate the total
    } catch (error) {
        console.error('Error applying discount:', error);
        this.discountError = 'An error occurred while applying the discount code';
    }
} 