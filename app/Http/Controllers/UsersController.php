<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UsersController extends Controller
{
    public function login()
    {
        if(Auth::check())
        {
            return redirect()->route('dashboard');
        }

        return response()
        ->view('login')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
    }

    public function register()
    {
        if(Auth::check())
        {
            return redirect()->route('dashboard');
        }

        return response()
        ->view('register')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
    }

    public function registerPost(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Redirect with a success message
        return redirect()->route('login')->with('success', 'Registration successful. Please log in.');
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log the user in
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard')->with('success', 'Logged in Successfully');
        }

        // If authentication fails, redirect back with an error
        return redirect()->back()->withErrors(['email' => 'Invalid email or password.']);
    }

    public function updateTerms(Request $request)
    {
        $validatedData = $request->validate([
            'terms' => 'required|string|in:approved',
        ]);

        // Get the authenticated user
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->terms !== 'pending') {
            return response()->json(['message' => 'Terms status is not pending'], 400);
        }

        // Update the user's terms status
        $user->terms = 'approved';

        if ($user->save()) {
            return response()->json(['message' => 'Terms updated successfully.']);
        } else {
            return response()->json(['message' => 'Failed to update terms'], 500);
        }
    }

    public function dashboard()
    {
        return view('auth.dashboard');
    }

    public function products()
    {
        $products = Products::orderBy('id', 'desc')->paginate(10);

        if ($products->isEmpty()) {
            return view('auth.products', [
                'products' => $products,
                'message' => 'No products available.'
            ]);
        }

        return view('auth.products', compact('products'));
    }


    public function addProduct(Request $request)
    {
        // Validate the incoming request data
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        // Attempt to create the product
        try {
            Products::create($data);

            return response()->json(['message' => 'Product added successfully.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add product: ' . $e->getMessage()], 500);
        }
    }

    public function updateProduct(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        // Find the product by ID
        $product = Products::find($id);

        // Check if product exists
        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        // Attempt to update the product
        try {
            $product->update($data);

            // Return a success response
            return response()->json(['message' => 'Product updated successfully.'], 200);
        } catch (\Exception $e) {
            // Handle any errors during the update process
            return response()->json(['error' => 'Failed to update product: ' . $e->getMessage()], 500);
        }
    }

    public function deleteProduct($id)
    {
        $product = Products::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        try {
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete product: ' . $e->getMessage()], 500);
        }
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));
            return response()->json(['message' => true, 'message' => 'File imported successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'message' => 'Failed to import products: ' . $e->getMessage()]);
        }
    }

    public function exportProducts()
    {
        try {
            // Fetch products from the database
            $products = Products::all();

            // Create a new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers for the Excel file
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Name');
            $sheet->setCellValue('C1', 'Price');

            // Write data to Excel
            $row = 2; // Start from the second row
            foreach ($products as $product) {
                $sheet->setCellValue('A' . $row, $product->id);
                $sheet->setCellValue('B' . $row, $product->name);
                $sheet->setCellValue('C' . $row, $product->price);
                $row++;
            }

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="products.xlsx"');
            header('Cache-Control: max-age=0');

            // Write the file to the output
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit; // Stop further execution
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to export products: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

}
