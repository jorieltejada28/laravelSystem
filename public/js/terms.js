const showTermsAndConditions = async () => {
    const { value: accept } = await Swal.fire({
        title: "Terms and Conditions",
        html: `
            <div style="max-height: 250px; overflow-y: auto; border: 3px solid black; padding: 10px; border-radius: 10px;">
                <p>Welcome to our service. By using our application, you agree to the following terms and conditions:</p>
                <h4>1. Acceptance of Terms</h4>
                <p>By accessing and using this application, you confirm that you have read, understood, and agree to be bound by these terms.</p>

                <h4>2. User Responsibilities</h4>
                <p>You are responsible for maintaining the confidentiality of your account and password. You agree to notify us immediately of any unauthorized use of your account or any other breach of security.</p>

                <h4>3. Modification of Terms</h4>
                <p>We reserve the right to modify these terms at any time. Any changes will be effective immediately upon posting the revised terms on this application. Your continued use of the application following such changes constitutes your acceptance of the new terms.</p>

                <h4>4. Privacy Policy</h4>
                <p>Your privacy is important to us. Our Privacy Policy outlines how we collect, use, and protect your personal information. By agreeing to these terms, you also consent to our Privacy Policy.</p>

                <h4>5. Limitation of Liability</h4>
                <p>In no event shall we be liable for any damages arising out of or in connection with your use of this application. This includes, but is not limited to, direct, indirect, incidental, or consequential damages.</p>

                <h4>6. Governing Law</h4>
                <p>These terms shall be governed by and construed in accordance with the laws of [Your Jurisdiction]. Any disputes arising under these terms will be subject to the exclusive jurisdiction of the courts located in [Your Jurisdiction].</p>

                <h4>7. Contact Information</h4>
                <p>If you have any questions about these Terms and Conditions, please contact us at [your email address].</p>
                <p>Please ensure you read and understand these terms before agreeing.</p>
            </div>
        `,
        input: "checkbox",
        inputValue: 0,
        inputPlaceholder: `I agree with the terms and conditions`,
        showCancelButton: false,
        allowOutsideClick: false,
        confirmButtonText: `Continue`,
        didOpen: () => {
            const confirmButton = Swal.getConfirmButton();
            confirmButton.disabled = true;

            const checkbox = Swal.getInput();
            checkbox.addEventListener('change', () => {
                confirmButton.disabled = !checkbox.checked;
            });
        },
        preConfirm: (value) => {
            if (!value) {
                Swal.showValidationMessage("You need to agree with T&C");
            }
            return value;
        }
    });

    if (accept) {
        try {
            const response = await axios.post('/update-terms', {
                terms: 'approved'
            });
            console.log(response.data);

            // Inject custom CSS for the green background toast
            const style = document.createElement('style');
            style.innerHTML = `
                .swal-toast-green {
                    background-color: #28a745 !important; /* Green color */
                    color: white !important; /* Text color */
                }
            `;
            document.head.appendChild(style);

            // Show success toast with green background
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'You have successfully accepted the terms and conditions.',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                customClass: {
                    popup: 'swal-toast-green'
                }
            }).then(() => {
                window.location.reload();
            });

        } catch (error) {
            console.error('Error updating terms:', error.response ? error.response.data : error.message);
            Swal.fire({
                title: 'Error!',
                text: error.response ? error.response.data.message : 'Failed to update terms. Please try again.',
                icon: 'error'
            });
        }
    }
};
