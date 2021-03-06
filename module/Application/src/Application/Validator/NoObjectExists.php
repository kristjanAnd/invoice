<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Application\Validator;

use Application\Entity\Subject\Company;
use Application\Util\StringUtil;
use Zend\Validator\Exception;
/**
 * Class that validates if objects does not exist in a given repository with a given list of matched fields
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @since   0.4.0
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class NoObjectExists extends \DoctrineModule\Validator\ObjectExists
{
    protected $company;

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }


    /**
     * Error constants
     */
    const ERROR_OBJECT_FOUND    = 'objectFound';

    /**
     * @var array Message templates
     */
    protected $messageTemplates = array(
        self::ERROR_OBJECT_FOUND    => "An object matching '%value%' was found",
    );

    protected $exclude;

    public function __construct(array $options)
    {
        if (array_key_exists('exclude', $options)) {
            if (is_array($options['exclude'])) {
                $this->exclude = $options['exclude'];
            } else {
                throw new Exception\InvalidArgumentException(
                    'Exclude parameter must be an array of values with assigned keys!'
                );
            }
        }
        parent::__construct($options);
    }

    /**
     * {@inheritDoc}
     */
    public function isValid($value)
    {
        if($this->company){
            $value = $this->company->getId() . '-' . StringUtil::urlify($value);
        }
        $value = $this->cleanSearchValue($value);
        if ($this->exclude != null) {
            if ($value == $this->exclude) {

                return true;
            }
        }

        $match = $this->objectRepository->findOneBy($value);

        if (is_object($match)) {
            $this->error(self::ERROR_OBJECT_FOUND, $value);

            return false;
        }

        return true;
    }
}
