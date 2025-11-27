<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;

class ProductController extends BaseController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $page = $this->request->getGet('page') ?: 1;
        
        $builder = $this->productModel->builder()
            ->select('p_id, p_code, p_name, p_barcode, p_image, p_standard_price, p_unit, p_created_at, p_updated_at');

        if ($keyword) {
            $builder->groupStart()
                ->like('p_code', $keyword)
                ->orLike('p_name', $keyword)
                ->orLike('p_barcode', $keyword)
                ->groupEnd();
        }

        $builder->orderBy('p_created_at', 'DESC');

        $total = $builder->countAllResults(false);
        $perPage = 10;
        $totalPages = ceil($total / $perPage);
        $data = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        return view('product/index', [
            'data' => $data,
            'keyword' => $keyword,
            'pager' => [
                'currentPage' => $page,
                'totalPages' => $totalPages
            ]
        ]);
    }

    public function create()
    {
        return view('product/form', [
            'isEdit' => false,
        ]);
    }

    public function edit($id)
    {
        $data = $this->productModel->find($id);
        
        if (!$data) {
            return redirect()->to(url_to('ProductController::index'))->with('error', '商品不存在');
        }
        
        return view('product/form', [
            'isEdit' => true,
            'data' => $data,
        ]);
    }

    public function save()
    {
        $data = $this->request->getPost();
        
        // 處理圖片上傳
        $image = $this->request->getFile('p_image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $image->move(ROOTPATH . 'public/uploads/products', $newName);
            $data['p_image'] = 'uploads/products/' . $newName;
        }
        
        $productId = $data['p_id'] ?? null;
        
        if ($productId) {
            // 更新
            if ($this->productModel->update($productId, $data)) {
                return redirect()->to(url_to('ProductController::index'))->with('success', '更新成功');
            } else {
                return redirect()->back()->withInput()->with('errors', $this->productModel->errors());
            }
        } else {
            // 新增
            if ($this->productModel->insert($data)) {
                return redirect()->to(url_to('ProductController::index'))->with('success', '新增成功');
            } else {
                return redirect()->back()->withInput()->with('errors', $this->productModel->errors());
            }
        }
    }

    public function delete($id)
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            return redirect()->to(url_to('ProductController::index'))->with('error', '商品不存在');
        }
        
        // 刪除圖片檔案
        if (!empty($product['p_image']) && file_exists(ROOTPATH . 'public/' . $product['p_image'])) {
            unlink(ROOTPATH . 'public/' . $product['p_image']);
        }
        
        $this->productModel->delete($id);
        return redirect()->to(url_to('ProductController::index'))->with('success', '刪除成功');
    }
}

