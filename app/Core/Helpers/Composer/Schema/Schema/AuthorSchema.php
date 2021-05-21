<?php


namespace App\Core\Helpers\Composer\Schema\Schema;


use Illuminate\Contracts\Support\Arrayable;

class AuthorSchema implements Arrayable
{

    /**
     * @var string|null
     */
    private ?string $name;

    /**
     * @var string|null
     */
    private ?string $email;

    /**
     * @var string|null
     */
    private ?string $homepage;

    /**
     * @var string|null
     */
    private ?string $role;

    /**
     * AuthorSchema constructor.
     * @param string|null $name
     * @param string|null $email
     * @param string|null $homepage
     * @param string|null $role
     */
    public function __construct(?string $name = null,
                                ?string $email = null,
                                ?string $homepage = null,
                                ?string $role = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->homepage = $homepage;
        $this->role = $role;
    }


    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getHomepage(): ?string
    {
        return $this->homepage;
    }

    /**
     * @param string|null $homepage
     */
    public function setHomepage(?string $homepage): void
    {
        $this->homepage = $homepage;
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param string|null $role
     */
    public function setRole(?string $role): void
    {
        $this->role = $role;
    }

    public function toArray()
    {
        return collect([
            'name' => $this->name,
            'email' => $this->email,
            'homepage' => $this->homepage,
            'role' => $this->role
        ])->filter(fn($val) => $val !== [] && $val !== null && ($val instanceof Collection ? $val->count() > 0 : true))->toArray();
    }
}
