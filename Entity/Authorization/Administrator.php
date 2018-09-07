<?php
/*
 * This file is part of the Beast package.
 *
 * (c) Maxwell Guo <beastmaxwell.guo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Beast\EasyAdminBundle\Entity\Authorization;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Administrator
 *
 * @ORM\Table(name="administrator")
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"username"},
 *     message="用户名已存在"
 * )
 */
class Administrator implements UserInterface, EquatableInterface, \Serializable
{
    public function __construct(Role $role = null)
    {
        $this->role = $role;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=32, nullable=false, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=64, nullable=true)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_super_admin", type="boolean", nullable=false)
     */
    private $isSuperAdmin = '0';

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="administrator", fetch="EAGER")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="cascade")
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="last_login_ip", type="string", length=32, nullable=true)
     */
    private $lastLoginIp;

    /**
     * @var string
     *
     * @ORM\Column(name="login_ip", type="string", length=32, nullable=true)
     */
    private $loginIp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="current_login", type="datetime", nullable=true)
     */
    private $currentLogin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param $id
     *
     * @return self
     */
    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return (bool)$this->isActive;
    }

    /**
     * @param boolean $isActive
     *
     * @return self
     */
    public function setIsActive($isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return self
     */
    public function setUsername($username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     *
     * @return self
     */
    public function setSalt($salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isIsSuperAdmin()
    {
        return $this->isSuperAdmin;
    }

    /**
     * @param boolean $isSuperAdmin
     *
     * @return self
     */
    public function setIsSuperAdmin($isSuperAdmin): self
    {
        $this->isSuperAdmin = $isSuperAdmin;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastLoginIp(): ?string
    {
        return $this->lastLoginIp;
    }

    /**
     * @param string $lastLoginIp
     *
     * @return self
     */
    public function setLastLoginIp($lastLoginIp): self
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    /**
     * @return string
     */
    public function getLoginIp(): ?string
    {
        return $this->loginIp;
    }

    /**
     * @param string $loginIp
     *
     * @return self
     */
    public function setLoginIp($loginIp): self
    {
        $this->loginIp = $loginIp;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }

    /**
     * @param \DateTime $lastLogin
     *
     * @return self
     */
    public function setLastLogin(\DateTime $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCurrentLogin(): ?DateTime
    {
        return $this->currentLogin;
    }

    /**
     * @param \DateTime $currentLogin
     *
     * @return self
     */
    public function setCurrentLogin(\DateTime $currentLogin): self
    {
        $this->currentLogin = $currentLogin;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @inheritDoc
     */
    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    public function setPassword(?string $password): self
    {
        if ($password) {
            $encoder = new BCryptPasswordEncoder(10);
            $password = $encoder->encodePassword($password, null);

            $this->password = $password;
        }

        return $this;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->salt
            ) = unserialize($serialized);
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return (bool)$this->isActive;
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->getPassword() !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }

        if ($this->isAccountNonExpired() !== $user->isAccountNonExpired()) {
            return false;
        }

        if ($this->isAccountNonLocked() !== $user->isAccountNonLocked()) {
            return false;
        }

        if ($this->isCredentialsNonExpired() !== $user->isCredentialsNonExpired()) {
            return false;
        }

        if ($this->isEnabled() !== $user->isEnabled()) {
            return false;
        }

        return true;
    }

    /**
     * @return Role
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     */
    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getRoleName(): string
    {
        $result = '暂无';
        if ($this->role) {
            $result = $this->role->getName();
        }
        return $result;
    }
}
