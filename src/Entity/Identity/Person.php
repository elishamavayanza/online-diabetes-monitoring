<?php

namespace App\Entity\Identity;

use App\Entity\Common\BaseEntity;
use App\Entity\Common\Gender;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Person extends BaseEntity
{
    #[ORM\Column(type: 'string', length: 150)]
    protected ?string $fullName = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    protected ?string $phone = null;

    #[ORM\Column(type: 'string', length: 50, enumType: Gender::class, nullable: true)]
    protected ?Gender $gender = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    protected ?string $avatarUrl = null;

    #[ORM\Embedded(class: Address::class, columnPrefix: 'address_')]
    protected ?Address $address = null;

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(?Gender $gender): static
    {
        $this->gender = $gender;
        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;
        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;
        return $this;
    }
}
