<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 6.05.15
 * Time: 17:27
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentRow
 *
 * @ORM\Table(name="document_row")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"invoice_row" = "Application\Entity\DocumentRow\InvoiceRow"})
 */
abstract class DocumentRow extends AbstractEntity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Application\Entity\Article
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Article")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     * })
     */
    protected $article;

    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @var float
     * @ORM\Column(name="quantity", type="decimal")
     */
    protected $quantity;

    /**
     * @var \Application\Entity\Unit
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Unit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unit_id", referencedColumnName="id")
     * })
     */
    protected $unit;

    /**
     * @var float
     * @ORM\Column(name="amount", type="decimal")
     */
    protected $amount;

    /**
     * @var \Application\Entity\Vat
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Vat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vat_id", referencedColumnName="id")
     * })
     */
    protected $vat;

    /**
     * @var float
     * @ORM\Column(name="vat_amount", type="decimal")
     */
    protected $vatAmount;

    /**
     * @var float
     * @ORM\Column(name="amount_vat", type="decimal")
     */
    protected $amountVat;


    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return float
     */
    public function getAmountVat()
    {
        return $this->amountVat;
    }

    /**
     * @param float $amountVat
     */
    public function setAmountVat($amountVat)
    {
        $this->amountVat = $amountVat;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param Article $article
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param Unit $unit
     */
    public function setUnit(Unit $unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return Vat
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param Vat $vat
     */
    public function setVat(Vat $vat)
    {
        $this->vat = $vat;
    }

    /**
     * @return float
     */
    public function getVatAmount()
    {
        return $this->vatAmount;
    }

    /**
     * @param float $vatAmount
     */
    public function setVatAmount($vatAmount)
    {
        $this->vatAmount = $vatAmount;
    }


} 