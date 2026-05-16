<?php

namespace App\Services;

use App\Repositories\ParkRepository;

class ParkService
{
    protected $repo;

    public function __construct(ParkRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getList(array $filters)
    {
        $query = $this->repo->query();

        // Tìm theo tên khu vui chơi hoặc địa điểm
        if (!empty($filters['search'])) {

            $search = trim($filters['search']);

            $query->where(function ($q) use ($search) {

                $q->where('ParkName', 'like', '%' . $search . '%')
                ->orWhere('Location', 'like', '%' . $search . '%');
            });
        }

        // Filter riêng theo địa điểm
        if (!empty($filters['location'])) {

            $location = trim($filters['location']);

            $query->where('Location', 'like', '%' . $location . '%');
        }

        // Status
        if (
            !isset($filters['status']) ||
            $filters['status'] === null ||
            $filters['status'] === ''
        ) {

            $query->where('Status', 1);

        } else {

            $query->where('Status', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 10;

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getDetail($id)
    {
        return $this->repo->findById($id);
    }

    public function create(array $data)
    {
        $data['OpenTime']  .= ':00';
        $data['CloseTime'] .= ':00';

        if (request()->hasFile('ImageFile')) {
            $file = request()->file('ImageFile');

            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('images'), $fileName);

            //$data['Image'] = '/images/' . $fileName;
            $data['Image'] = url('/images/' . $fileName);
        }

        $data['Status'] = 1;

        unset($data['ImageFile']);

        return $this->repo->create($data);
    }

    public function update($park, array $data)
    {
        $data['OpenTime']  .= ':00';
        $data['CloseTime'] .= ':00';

        $file = request()->file('ImageFile');

        if ($file) {

        if (!empty($park->Image)) {
            $path = str_replace(url('/'), '', $park->Image);

            if (file_exists(public_path($path))) {
                unlink(public_path($path));
            }
        }

        $file = request()->file('ImageFile');

        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $file->move(public_path('images'), $fileName);

        $data['Image'] = url('/images/' . $fileName); // ✅ FIX
    }

        unset($data['ImageFile']);

        return $this->repo->update($park, $data);
    }

    public function delete($park)
    {
        if (!empty($park->Image)) {
            $path = str_replace(url('/'), '', $park->Image);

            if (file_exists(public_path($path))) {
                unlink(public_path($path));
            }
        }

        return $this->repo->delete($park);
    }
}