// Get CSRF token from the Blade template
const csrfToken = '{{ csrf_token() }}';

// View Product Function
const viewProduct = (id, name, description, price) => {
    Swal.fire({
        title: 'Product Details',
        html: `
            <p><strong>ID:</strong> ${id}</p>
            <p><strong>Name:</strong> ${name}</p>
            <p><strong>Description:</strong> ${description}</p>
            <p><strong>Price:</strong> ₱${parseFloat(price).toFixed(2)}</p>
        `,
        icon: 'info',
        showCloseButton: true,
        showConfirmButton: false
    });
};

document.getElementById('addProductBtn').addEventListener('click', function() {
    const showInputDialog = (name = '', description = '', price = '') => {
        Swal.fire({
            title: 'Add Product',
            html: `
                <input type="text" id="productName" class="swal2-input" placeholder="Product Name" value="${name}">
                <input type="text" id="productDescription" class="swal2-input" placeholder="Description" value="${description}">
                <input type="text" id="productPrice" class="swal2-input" placeholder="Price" value="${price}">
            `,
            focusConfirm: false,
            confirmButtonText: 'Confirm',
            showCancelButton: true,
            preConfirm: () => {
                const name = document.getElementById('productName').value;
                const description = document.getElementById('productDescription').value;
                const price = document.getElementById('productPrice').value;

                // Validate inputs
                if (!name || !description || !price) {
                    Swal.showValidationMessage('Please fill in all fields');
                    return false; // Prevent further execution
                }
                return { name, description, price: parseFloat(price) }; // Return the input values
            }
        }).then(result => {
            if (result.isConfirmed) {
                const { name, description, price } = result.value;
                confirmProductInput(name, description, price);
            }
        });
    };

    const confirmProductInput = (name, description, price) => {
        Swal.fire({
            title: 'Please confirm your input',
            html: `
                <p><strong>Product Name:</strong> ${name}</p>
                <p><strong>Description:</strong> ${description}</p>
                <p><strong>Price:</strong> ₱${price.toFixed(2)}</p>
                <p>Please check if you have made any mistakes.</p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Confirm and Add',
            cancelButtonText: 'Edit'
        }).then(result => {
            if (result.isConfirmed) {
                addProduct(name, description, price);
            } else {
                showInputDialog(name, description, price); // Prompt for editing
            }
        });
    };

    const addProduct = (name, description, price) => {
        fetch(`{{ route('products.add') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken // Use the CSRF token here
            },
            body: JSON.stringify({ name, description, price })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.error || 'Failed to add product. Please try again.');
                });
            }
            return response.json();
        })
        .then(() => {
            // Set a flag in localStorage before reloading
            localStorage.setItem('productAdded', 'true');
            location.reload(); // Reload the page
        })
        .catch(error => {
            Swal.fire('Error', error.message, 'error');
        });
    };

    const showSuccessToast = (message) => {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            background: "#28a745",
            color: "#fff",
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({
            icon: "success",
            title: message
        });
    };

    // Show the input dialog initially
    showInputDialog();
});

// Check for the flag after the page loads
window.addEventListener('load', () => {
    if (localStorage.getItem('productAdded')) {
        showSuccessToast('Product added successfully!');
        localStorage.removeItem('productAdded'); // Clear the flag
    }
});

// Edit Product Function
const editProduct = (id, name, description, price) => {
    const showEditDialog = () => {
        Swal.fire({
            title: 'Edit Product',
            html: `
                <input type="text" id="editProductName" class="swal2-input" placeholder="Product Name" value="${name}">
                <input type="text" id="editProductDescription" class="swal2-input" placeholder="Description" value="${description}">
                <input type="text" id="editProductPrice" class="swal2-input" placeholder="Price" value="${price}">
            `,
            focusConfirm: false,
            confirmButtonText: 'Update',
            showCancelButton: true,
            preConfirm: () => {
                const name = document.getElementById('editProductName').value;
                const description = document.getElementById('editProductDescription').value;
                const price = document.getElementById('editProductPrice').value;

                if (!name || !description || !price) {
                    Swal.showValidationMessage('Please fill in all fields');
                    return false; // Prevent further execution
                }

                return confirmEditProductInput(id, name, description, price);
            }
        });
    };

    const confirmEditProductInput = (id, name, description, price) => {
        return Swal.fire({
            title: 'Please confirm your input',
            html: `
                <p><strong>Product Name:</strong> ${name}</p>
                <p><strong>Description:</strong> ${description}</p>
                <p><strong>Price:</strong> ₱${parseFloat(price).toFixed(2)}</p>
                <p>Please check if you have made any mistakes.</p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Confirm and Update',
            cancelButtonText: 'Edit'
        }).then(result => {
            if (result.isConfirmed) {
                return updateProduct(id, name, description, price);
            } else {
                // User opted to edit their input
                showEditDialog();
            }
        });
    };

    const updateProduct = (id, name, description, price) => {
        return fetch(`{{ url('/products') }}/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken // Use the CSRF token here
            },
            body: JSON.stringify({
                name: name,
                description: description,
                price: parseFloat(price)
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.error || 'Failed to update product. Please try again.');
                });
            }
            return response.json();
        })
        .then(() => {
            // Set a flag in localStorage before reloading
            localStorage.setItem('productUpdated', 'true');
            location.reload(); // Reload the page
        })
        .catch(error => {
            Swal.fire('Error', error.message, 'error');
        });
    };

    showEditDialog();
};

// Check for the flag after the page loads
window.addEventListener('load', () => {
    if (localStorage.getItem('productUpdated')) {
        showSuccessToast('Product updated successfully!');
        localStorage.removeItem('productUpdated'); // Clear the flag
    }
});

// Delete Product Function
const deleteProduct = (id) => {
    Swal.fire({
        title: 'Are you sure you want to delete this product?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with the deletion
            fetch(`/products/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken // Use the CSRF token here
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Failed to delete product.');
                    });
                }
                return response.json();
            })
            .then(() => {
                // Set a flag in localStorage before reloading
                localStorage.setItem('productDeleted', 'true');
                location.reload(); // Reload the page
            })
            .catch(error => {
                Swal.fire('Error', error.message, 'error');
            });
        }
    });
};

// Check for the flag after the page loads
window.addEventListener('load', () => {
    if (localStorage.getItem('productDeleted')) {
        showSuccessToast('Product deleted successfully!');
        localStorage.removeItem('productDeleted'); // Clear the flag
    }
});
