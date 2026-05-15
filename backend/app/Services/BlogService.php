<?php

namespace App\Services;

use App\Repositories\BlogRepository;

class BlogService
{
    protected $repository;

    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index($request)
    {
        return $this->repository->index($request);
    }

    public function show($id)
    {
        return $this->repository->find($id);
    }

    public function store($request)
    {
        $bannerImage = null;

        // upload ảnh
        if ($request->hasFile('BannerImage')) {

            $file = $request->file('BannerImage');

            $fileName =
            'blog_' .
            time() .
            '_' .
            $file->getClientOriginalName();

            $destinationPath = base_path('../frontend/public/images');

            $file->move($destinationPath, $fileName);

            $bannerImage = '/images/' . $fileName;
        }

        return $this->repository->store([
            'Title' => $request->Title,
            'BannerImage' => $bannerImage,
            'Summary' => $request->Summary,
            'Content' => $request->Content,
            'Tags' => $request->Tags,
            'ParkID' => $request->ParkID,
            'UserID' => $request->UserID,
            'Status' => $request->Status ?? 1,
        ]);
    }

    public function update($id, $request)
    {
        $blog = $this->repository->find($id);

        if (!$blog) {
            return null;
        }

        $bannerImage = $blog->BannerImage;

        // UPLOAD ẢNH MỚI
        if ($request->hasFile('BannerImage')) {

            $file = $request->file('BannerImage');

            // TÊN FILE MỚI
            $fileName =
                'blog_' .
                time() .
                '_' .
                uniqid() .
                '.' .
                $file->getClientOriginalExtension();

            // THƯ MỤC LƯU
            $destinationPath =
                base_path('../frontend/public/images');

            // TẠO THƯ MỤC NẾU CHƯA CÓ
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            // MOVE FILE
            $file->move($destinationPath, $fileName);

            // URL
            $bannerImage = '/images/' . $fileName;
        }

        return $this->repository->update($id, [
            'Title' => $request->Title,
            'BannerImage' => $bannerImage,
            'Summary' => $request->Summary,
            'Content' => $request->Content,
            'Tags' => $request->Tags,
            'ParkID' => $request->ParkID,
            'UserID' => $request->UserID,
            'Status' => $request->Status,
        ]);
    }


    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}