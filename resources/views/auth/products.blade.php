@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container mt-5">
    <h1 class="text-center">Products</h1>
    <div class="row">
        <div class="col-md-12 mb-3 d-flex align-items-center">
            <button class="btn btn-primary me-2" id="addProductBtn">Add Product</button>
            <button class="btn btn-success me-2" id="exportProductBtn">Export Products</button>

            <!-- Import form -->
            <form id="importProductForm" enctype="multipart/form-data" class="d-inline">
                <!-- Hidden file input -->
                <input type="file" id="fileInput" name="file" style="display: none;" required>

                <!-- Import button -->
                <button type="button" class="btn btn-danger" id="importProductBtn">Import Product</button>
            </form>
        </div>
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->description }}</td>
                            <td>₱{{ number_format($product->price, 2) }}</td>
                            <td class="text-center" style="white-space: nowrap;">
                                <a class="btn btn-primary btn-sm" onclick="viewProduct({{ $product->id }}, '{{ $product->name }}', '{{ $product->description }}', '{{ $product->price }}')">View</a>
                                <a class="btn btn-success btn-sm" onclick="editProduct({{ $product->id }}, '{{ $product->name }}', '{{ $product->description }}', '{{ $product->price }}')">Edit</a>
                                <a class="btn btn-danger btn-sm" onclick="deleteProduct({{ $product->id }})">Delete</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">{{ $message ?? 'No products available.' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<script>

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

                    if (!name || !description || !price) {
                        Swal.showValidationMessage('Please fill in all fields');
                        return false;
                    }
                    return { name, description, price: parseFloat(price) };
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
                    showInputDialog(name, description, price);
                }
            });
        };

        const addProduct = (name, description, price) => {
            fetch('{{ route('products.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                localStorage.setItem('productAdded', 'true');
                location.reload();
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

        showInputDialog();
    });

    // Check for the flag after the page loads
    window.addEventListener('load', () => {
        if (localStorage.getItem('productAdded')) {
            showSuccessToast('Product added successfully!');
            localStorage.removeItem('productAdded');
        }
    });

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
                        return false;
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
                    showEditDialog();
                }
            });
        };

        const updateProduct = (id, name, description, price) => {
            return fetch(`{{ url('/products') }}/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name, description, price: parseFloat(price) })
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
                localStorage.setItem('productUpdated', 'true');
                location.reload();
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
            localStorage.removeItem('productUpdated');
        }
    });

    const deleteProduct = (id) => {
        Swal.fire({
            title: 'Are you sure you want to delete this product?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#716add',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/products/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                    localStorage.setItem('productDeleted', 'true');
                    location.reload();
                })
                .catch(error => {
                    Swal.fire('Error', error.message, 'error');
                });
            }
        });
    };

    // Check for flags after the page loads
    window.addEventListener('load', () => {
        if (localStorage.getItem('productDeleted')) {
            showSuccessToast('Product deleted successfully!');
            localStorage.removeItem('productDeleted');
        }

        if (localStorage.getItem('productUpdated')) {
            showSuccessToast('Product updated successfully!');
            localStorage.removeItem('productUpdated');
        }
    });

    document.getElementById('importProductBtn').addEventListener('click', function() {
        document.getElementById('fileInput').click(); // Trigger file input
    });

    document.getElementById('fileInput').addEventListener('change', function() {
        if (this.files.length > 0) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to import this file?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, import it!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('file', this.files[0]);

                    fetch('{{ route("import.products") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Failed to import file.');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.message) {
                            localStorage.setItem('fileImported', 'true');
                            location.reload();
                        } else {
                            toastr.error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('There was an error uploading the file.');
                    });
                }
            });
        }
    });

    // Check for the flag after the page loads
    window.addEventListener('load', () => {
        if (localStorage.getItem('fileImported')) {
            showSuccessToast('File imported successfully!');
            localStorage.removeItem('fileImported');
        }
    });

    // Export Products Function
    document.getElementById('exportProductBtn').addEventListener('click', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Do you want to export all products?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, export it!',
            cancelButtonText: 'No, cancel!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("export.products") }}', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        return response.blob();
                    } else {
                        return response.json().then(data => {
                            throw new Error(data.error || 'Failed to export products.');
                        });
                    }
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'products.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();

                    localStorage.setItem('productsExported', 'true');
                    location.reload();
                })
                .catch(error => {
                    toastr.error('Error: ' + error.message);
                });
            }
        });
    });

    // Check for the flag after the page loads
    window.addEventListener('load', () => {
        if (localStorage.getItem('productsExported')) {
            showSuccessToast('Products exported successfully!');
            localStorage.removeItem('productsExported');
        }
    });

</script>
@endsection
