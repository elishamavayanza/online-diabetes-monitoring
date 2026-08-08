<?php

namespace App\Mapper\Healthcare;

use App\DTO\Request\Healthcare\DepartmentRequestDTO;
use App\DTO\Response\Healthcare\DepartmentResponseDTO;
use App\Entity\Healthcare\Department;
use App\Entity\Healthcare\HealthcareFacility;

class DepartmentMapper
{
    public function mapRequestToEntity(DepartmentRequestDTO $dto, HealthcareFacility $facility, ?Department $department = null): Department
    {
        $department ??= new Department();

        $department->setFacility($facility);
        $department->setName($dto->name);
        $department->setSpecialty($dto->specialty);

        return $department;
    }

    public function mapEntityToResponse(Department $department): DepartmentResponseDTO
    {
        return DepartmentResponseDTO::fromEntity($department);
    }
}
