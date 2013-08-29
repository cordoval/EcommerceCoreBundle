<?php

namespace Ecommerce\Bundle\CoreBundle\Doctrine\Orm;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Ecommerce\Bundle\CoreBundle\Doctrine\Orm\CartRepository")
 * @ORM\Table(name="ecommerce_cart")
 * @ORM\HasLifecycleCallbacks
 */
class Cart
{
    const STATUS_EXPIRED = 0;

    const STATUS_OPEN = 1;

    const STATUS_CHECKOUT = 2;


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    private $status;

    /**
     * @var CartItem[]
     *
     * @ORM\OneToMany(targetEntity="CartItem", mappedBy="cart", cascade={"persist", "remove", "merge"}) // , fetch="EAGER"
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     */
    private $items;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_items", type="smallint", nullable=false)
     */
    private $totalItems;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="decimal", precision=6, scale=2, nullable=false)
     */
    private $total;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=false)
     */
    private $expiresAt;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->status = self::STATUS_OPEN;

        $this->items = new ArrayCollection();

        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();

        $this->setExpiresAt();
    }



    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param integer $status
     * @return Cart
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }


    /**
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * @param CartItem $item
     * @return Cart
     */
    public function addItem(CartItem $item)
    {
        $this->items[] = $item;

        $this->setExpiresAt();

        return $this;
    }


    /**
     * @param CartItem $item
     * @return Cart
     */
    public function removeItem(CartItem $item)
    {
        $this->items->removeElement($item);

        $this->setExpiresAt();

        return $this;
    }


    /**
     * @param integer $totalItems
     * @return Cart
     */
    public function setTotalItems($totalItems)
    {
        $this->totalItems = $totalItems;

        return $this;
    }


    /**
     * @return integer
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }


    /**
     * @param float $total
     * @return Cart
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }


    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }


    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function calculateTotals()
    {
        $totalItems = 0;
        $total      = 0;

        foreach ($this->getItems() as $item) {
            $totalItems++;
            $total += (float)$item->getPrice();
        }

        $this->totalItems = $totalItems;
        $this->total      = $total;
    }


    /**
     * @return CartItem[]
     */
    public function getItems()
    {
        return $this->items;
    }


    /**
     * @param \DateTime $createdAt
     *
     * @return Cart
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt instanceof \DateTime ? $createdAt : new \DateTime();

        return $this;
    }


    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    /**
     * @ORM\PreUpdate
     *
     * @param \DateTime|null $updatedAt
     * @return Cart
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt instanceof \DateTime ? $updatedAt : new \DateTime();

        return $this;
    }


    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * @param \DateTime|null $expiresAt
     * @return Cart
     */
    public function setExpiresAt(\DateTime $expiresAt = null)
    {
        if (!$expiresAt instanceof \DateTime) {
            $expiresAt = new \DateTime();
            $expiresAt->add(new \DateInterval('PT3H'));
        }

        $this->expiresAt = $expiresAt;

        return $this;
    }


    /**
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }


    /**
     * @return boolean
     */
    public function isExpired()
    {
        return $this->getExpiresAt() < new \DateTime('now');
    }
}
