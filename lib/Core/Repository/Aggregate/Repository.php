<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Repository\Aggregate;

use eZ\Publish\API\Repository\Repository as RepositoryInterface;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Values\User\UserReference;
use Closure;
use RuntimeException;

/**
 * Aggregate implementation of Repository interface.
 *
 * When current Repository User is changed, any custom instance of Repository will not be updated.
 * This implementation takes care of it by aggregating custom instances to update current User when
 * necessary. For that to work, it must be registered as the top one, meaning that service alias
 * 'ezpublish.api.repository' must point to it. This is taken care of by AggregateRepositoryPass.
 *
 * @see \Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler\AggregateRepositoryPass
 */
class Repository implements RepositoryInterface
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $ezRepository;

    /**
     * @var \eZ\Publish\API\Repository\Repository[]
     */
    private $customRepositories;

    /**
     * Construct repository object from top eZ Platform Repository and
     * an array of custom Repositories
     *
     * @param \eZ\Publish\API\Repository\Repository $ezRepository
     * @param \eZ\Publish\API\Repository\Repository[] $customRepositories
     */
    public function __construct(
        RepositoryInterface $ezRepository,
        array $customRepositories = []
    ) {
        $this->ezRepository = $ezRepository;
        $this->customRepositories = $customRepositories;
    }

    public function getCurrentUser()
    {
        return $this->ezRepository->getCurrentUser();
    }

    public function getCurrentUserReference()
    {
        return $this->ezRepository->getCurrentUserReference();
    }

    public function setCurrentUser(UserReference $user)
    {
        foreach ($this->customRepositories as $customRepository) {
            $customRepository->setCurrentUser($user);
        }

        return $this->ezRepository->setCurrentUser($user);
    }

    public function sudo(callable $callback, RepositoryInterface $outerRepository = null)
    {
        return $this->ezRepository->sudo(
            $callback,
            $outerRepository instanceof RepositoryInterface ? $outerRepository : $this
        );
    }

    public function hasAccess($module, $function, UserReference $user = null)
    {
        return $this->ezRepository->hasAccess($module, $function, $user);
    }

    public function canUser($module, $function, ValueObject $object, $targets = null)
    {
        return $this->ezRepository->canUser($module, $function, $object, $targets);
    }

    public function getBookmarkService()
    {
        if (!method_exists($this->ezRepository, 'getBookmarkService')) {
            throw new RuntimeException(sprintf('getBookmarkService method does not exist in %s class', get_class($this->ezRepository)));
        }

        return $this->ezRepository->getBookmarkService();
    }

    public function getNotificationService()
    {
        if (!method_exists($this->ezRepository, 'getNotificationService')) {
            throw new RuntimeException(sprintf('getNotificationService method does not exist in %s class', get_class($this->ezRepository)));
        }

        return $this->ezRepository->getNotificationService();
    }

    public function getUserPreferenceService()
    {
        if (!method_exists($this->ezRepository, 'getUserPreferenceService')) {
            throw new RuntimeException(sprintf('getUserPreferenceService method does not exist in %s class', get_class($this->ezRepository)));
        }

        return $this->ezRepository->getUserPreferenceService();
    }

    public function getContentService()
    {
        return $this->ezRepository->getContentService();
    }

    public function getContentLanguageService()
    {
        return $this->ezRepository->getContentLanguageService();
    }

    public function getContentTypeService()
    {
        return $this->ezRepository->getContentTypeService();
    }

    public function getLocationService()
    {
        return $this->ezRepository->getLocationService();
    }

    public function getTrashService()
    {
        return $this->ezRepository->getTrashService();
    }

    public function getSectionService()
    {
        return $this->ezRepository->getSectionService();
    }

    public function getUserService()
    {
        return $this->ezRepository->getUserService();
    }

    public function getURLAliasService()
    {
        return $this->ezRepository->getURLAliasService();
    }

    public function getURLWildcardService()
    {
        return $this->ezRepository->getURLWildcardService();
    }

    public function getObjectStateService()
    {
        return $this->ezRepository->getObjectStateService();
    }

    public function getRoleService()
    {
        return $this->ezRepository->getRoleService();
    }

    public function getSearchService()
    {
        return $this->ezRepository->getSearchService();
    }

    public function getFieldTypeService()
    {
        return $this->ezRepository->getFieldTypeService();
    }

    public function getURLService()
    {
        return $this->ezRepository->getURLService();
    }

    public function getPermissionResolver()
    {
        return $this->ezRepository->getPermissionResolver();
    }

    public function beginTransaction()
    {
        return $this->ezRepository->beginTransaction();
    }

    public function commit()
    {
        return $this->ezRepository->commit();
    }

    public function rollback()
    {
        return $this->ezRepository->rollback();
    }

    public function commitEvent($event)
    {
        return $this->ezRepository->commitEvent($event);
    }

    public function createDateTime($timestamp = null)
    {
        return $this->ezRepository->createDateTime($timestamp);
    }
}
