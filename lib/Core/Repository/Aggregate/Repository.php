<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Repository\Aggregate;

use eZ\Publish\API\Repository\BookmarkService;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\NotificationService;
use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Repository as RepositoryInterface;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\TrashService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\URLService;
use eZ\Publish\API\Repository\URLWildcardService;
use eZ\Publish\API\Repository\UserPreferenceService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\API\Repository\Values\User\UserReference;
use eZ\Publish\API\Repository\Values\ValueObject;
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
     * an array of custom Repositories.
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

    public function getCurrentUser(): User
    {
        return $this->ezRepository->getCurrentUser();
    }

    public function getCurrentUserReference(): UserReference
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

    public function canUser($module, $function, ValueObject $object, $targets = null): bool
    {
        return $this->ezRepository->canUser($module, $function, $object, $targets);
    }

    public function getBookmarkService(): BookmarkService
    {
        if (!\method_exists($this->ezRepository, 'getBookmarkService')) {
            throw new RuntimeException(\sprintf('getBookmarkService method does not exist in %s class', \get_class($this->ezRepository)));
        }

        return $this->ezRepository->getBookmarkService();
    }

    public function getNotificationService(): NotificationService
    {
        if (!\method_exists($this->ezRepository, 'getNotificationService')) {
            throw new RuntimeException(\sprintf('getNotificationService method does not exist in %s class', \get_class($this->ezRepository)));
        }

        return $this->ezRepository->getNotificationService();
    }

    public function getUserPreferenceService(): UserPreferenceService
    {
        if (!\method_exists($this->ezRepository, 'getUserPreferenceService')) {
            throw new RuntimeException(\sprintf('getUserPreferenceService method does not exist in %s class', \get_class($this->ezRepository)));
        }

        return $this->ezRepository->getUserPreferenceService();
    }

    public function getContentService(): ContentService
    {
        return $this->ezRepository->getContentService();
    }

    public function getContentLanguageService(): LanguageService
    {
        return $this->ezRepository->getContentLanguageService();
    }

    public function getContentTypeService(): ContentTypeService
    {
        return $this->ezRepository->getContentTypeService();
    }

    public function getLocationService(): LocationService
    {
        return $this->ezRepository->getLocationService();
    }

    public function getTrashService(): TrashService
    {
        return $this->ezRepository->getTrashService();
    }

    public function getSectionService(): SectionService
    {
        return $this->ezRepository->getSectionService();
    }

    public function getUserService(): UserService
    {
        return $this->ezRepository->getUserService();
    }

    public function getURLAliasService(): URLAliasService
    {
        return $this->ezRepository->getURLAliasService();
    }

    public function getURLWildcardService(): URLWildcardService
    {
        return $this->ezRepository->getURLWildcardService();
    }

    public function getObjectStateService(): ObjectStateService
    {
        return $this->ezRepository->getObjectStateService();
    }

    public function getRoleService(): RoleService
    {
        return $this->ezRepository->getRoleService();
    }

    public function getSearchService(): SearchService
    {
        return $this->ezRepository->getSearchService();
    }

    public function getFieldTypeService(): FieldTypeService
    {
        return $this->ezRepository->getFieldTypeService();
    }

    public function getURLService(): URLService
    {
        return $this->ezRepository->getURLService();
    }

    public function getPermissionResolver(): PermissionResolver
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
}
