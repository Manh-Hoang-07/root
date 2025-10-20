<?php

namespace App\Services\Traits;

/**
 * Trait containing hook methods for service operations
 */
trait HasServiceHooks
{
    /**
     * Hook method called when create operation succeeds
     * Override in child classes to add custom logic
     * 
     * @param array $result The created record
     * @param array $data The original data used for creation
     */
    protected function onCreateSuccess(array $result, array $data): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when create operation fails
     * Override in child classes to add custom error handling
     * 
     * @param array $data The original data used for creation
     */
    protected function onCreateFail(array $data): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when update operation succeeds
     * Override in child classes to add custom logic
     * 
     * @param array $result The updated record
     * @param mixed $id The ID of the updated record
     * @param array $data The original data used for update
     */
    protected function onUpdateSuccess(array $result, $id, array $data): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when update operation fails
     * Override in child classes to add custom error handling
     * 
     * @param mixed $id The ID of the record that failed to update
     * @param array $data The original data used for update
     */
    protected function onUpdateFail($id, array $data): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when delete operation succeeds
     * Override in child classes to add custom logic
     * 
     * @param array|null $item The deleted record (before deletion)
     * @param mixed $id The ID of the deleted record
     */
    protected function onDeleteSuccess(?array $item, $id): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when delete operation fails
     * Override in child classes to add custom error handling
     * 
     * @param mixed $id The ID of the record that failed to delete
     * @param array|null $item The record that failed to delete
     */
    protected function onDeleteFail($id, ?array $item): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when createOrUpdate operation succeeds
     * Override in child classes to add custom logic
     * 
     * @param array $result The created/updated record
     * @param array $conditions The conditions used for lookup
     * @param array $data The original data used for create/update
     */
    protected function onCreateOrUpdateSuccess(array $result, array $conditions, array $data): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when createOrUpdate operation fails
     * Override in child classes to add custom error handling
     * 
     * @param array $conditions The conditions used for lookup
     * @param array $data The original data used for create/update
     */
    protected function onCreateOrUpdateFail(array $conditions, array $data): void
    {
        // Override in child classes if needed
    }
}