<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\ProductCategoryModel;

class ProductController extends BaseController
{
    private $productModel;
    private $productCategoryModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->productCategoryModel = new ProductCategoryModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $page = $this->request->getGet('page') ?: 1;

        $result = $this->productModel->getProductsWithCategory($keyword, $page);

        return view('product/index', [
            'data' => $result['data'],
            'keyword' => $keyword,
            'pager' => [
                'currentPage' => $result['currentPage'],
                'totalPages' => $result['totalPages']
            ]
        ]);
    }

    public function create()
    {
        $categories = $this->productCategoryModel->getAllForDropdown();

        return view('product/form', [
            'isEdit' => false,
            'categories' => $categories,
        ]);
    }

    public function edit($id)
    {
        $data = $this->productModel->getProductWithCategory($id);

        if (!$data) {
            return redirect()->to(url_to('ProductController::index'))->with('error', '商品不存在');
        }

        $categories = $this->productCategoryModel->getAllForDropdown();

        return view('product/form', [
            'isEdit' => true,
            'data' => $data,
            'categories' => $categories,
        ]);
    }

    public function show($id)
    {
        $data = $this->productModel->getProductWithCategory($id);

        if (!$data) {
            return redirect()->to(url_to('ProductController::index'))->with('error', '商品不存在');
        }

        return view('product/show', [
            'data' => $data,
        ]);
    }

    public function save()
    {
        $requestData = $this->request->getPost();
        $productId = $requestData['p_id'] ?? null;
        $productId = $productId !== null && $productId !== '' ? (int) $productId : null;

        if ($productId && !$this->productModel->find($productId)) {
            return redirect()->to(url_to('ProductController::index'))->with('error', '商品不存在');
        }

        $rules = $this->productModel->getValidationRules();
        $messages = $this->productModel->getValidationMessages();

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $payload = [
            'p_pc_id' => $requestData['p_pc_id'] ?? null,
            'p_name' => trim((string) ($requestData['p_name'] ?? '')),
            'p_supplier' => trim((string) ($requestData['p_supplier'] ?? '')),
            'p_style' => trim((string) ($requestData['p_style'] ?? '')),
            'p_color' => trim((string) ($requestData['p_color'] ?? '')),
            'p_size' => trim((string) ($requestData['p_size'] ?? '')),
            'p_specifications' => trim((string) ($requestData['p_specifications'] ?? '')),
            'p_standard_price' => $requestData['p_standard_price'] ?? 0,
            'p_cost_price' => $requestData['p_cost_price'] ?? 0,
        ];

        // 處理圖片上傳
        $image = $this->request->getFile('p_image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            try {
                $newName = $image->getRandomName();
                $image->move(ROOTPATH . 'uploads/products', $newName);
                $payload['p_image'] = 'uploads/products/' . $newName;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', '圖片上傳失敗：' . $e->getMessage());
            }
        }

        try {
            if ($productId) {
                $this->productModel->update($productId, $payload);
            } else {
                $this->productModel->insert($payload);
            }

            return redirect()->to(url_to('ProductController::index'));
        } catch (\Throwable $th) {
            log_message('error', 'Product save failed: {message}', [
                'message' => $th->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', '儲存失敗：' . $th->getMessage());
        }
    }

    public function delete($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to(url_to('ProductController::index'))->with('error', '商品不存在');
        }

        try {
            // 刪除圖片檔案
            if (!empty($product['p_image']) && file_exists(ROOTPATH . 'public/' . $product['p_image'])) {
                unlink(ROOTPATH . 'public/' . $product['p_image']);
            }

            $this->productModel->delete($id);

            return redirect()->to(url_to('ProductController::index'));
        } catch (\Throwable $th) {
            log_message('error', 'Product delete failed: {message}', [
                'message' => $th->getMessage(),
            ]);

            return redirect()->to(url_to('ProductController::index'))->with('error', '刪除失敗：' . $th->getMessage());
        }
    }
}
