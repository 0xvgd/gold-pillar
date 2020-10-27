<?php

namespace App\EntityListener;

use App\Entity\Helper\LogChanges;
use App\Entity\Security\User;
use Doctrine\ORM\Event\OnFlushEventArgs;
use ReflectionClass;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * LogListener.
 *
 * @author Laerte Mercier <laertejjunior@gmail.com>
 */
abstract class LogListener
{
    private $tokenStorage;

    public function __construct(
        TokenStorageInterface $tokenStorage
    ) {
        $this->tokenStorage = $tokenStorage;
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();
        $updatedEntities = $unitOfWork->getScheduledEntityUpdates();

        foreach ($updatedEntities as $updatedEntity) {
            $class = new ReflectionClass($this->getEntityClass());
            if ($class->isInstance($updatedEntity)) {
                $changeset = $unitOfWork->getEntityChangeSet($updatedEntity);

                foreach ($this->getFields() as $field) {
                    $this->saveLog($entityManager, $changeset, $unitOfWork, $updatedEntity, $field);
                }
            }
        }
    }

    protected function saveLog(
        $entityManager,
        $changeset,
        $unitOfWork,
        $updatedEntity,
        $field
    ) {
        $user = $this->getUser();

        $updatedEntityMetaData = $entityManager->getClassMetadata(get_class($updatedEntity));

        if (!is_array($changeset)) {
            return null;
        }

        if (array_key_exists($field, $changeset)) {
            $changes = $changeset[$field];
            $previousValueForField = array_key_exists(0, $changes) ? $changes[0] : null;
            $newValueForField = array_key_exists(1, $changes) ? $changes[1] : null;
            $fieldType = $updatedEntityMetaData->getTypeOfField($field);

            if ($previousValueForField != $newValueForField) {
                $logChanges = new LogChanges();
                $logChanges->setOldValue($previousValueForField);
                $logChanges->setNewValue($newValueForField);
                $logChanges->setClassName($this->getEntityClass());
                $logChanges->setEntityId($updatedEntity->getId());
                $logChanges->setUser($user);
                $logChanges->setField($field);
                $logChanges->setFieldType($fieldType);
                $logChanges->setCreatedAt(new \DateTime());
                $entityManager->persist($logChanges);
                $metaData = $entityManager->getClassMetadata(LogChanges::class);
                $unitOfWork->computeChangeSet($metaData, $logChanges);
            }
        }
    }

    /**
     * return entity name.
     *
     * @return string
     */
    abstract protected function getEntityClass();

    /**
     * return fields.
     *
     * @return array
     */
    abstract protected function getFields();

    /**
     * @return User
     */
    abstract protected function getUser();
}
