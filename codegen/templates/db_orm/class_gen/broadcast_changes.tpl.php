<?php
/**
 * Primitives to broadcast changes. These use the watcher mechanism. Override if you want something different or more
 * granular.
 */

if (count($objTable->PrimaryKeyColumnArray) == 1) {
    $pkType = $objTable->PrimaryKeyColumnArray[0]->VariableType;

    if ($pkType == 'integer') {
        $pkType = 'int';
    }
} else {
    $pkType = 'string';	// combined pk
}
?>


    /**
    * Broadcast a notification when a new record is inserted into the table.
    *
    * @param <?= $pkType ?> $pk The primary key of the record being inserted.
    * @return void
    */
    protected static function broadcastInsert(<?= $pkType ?> $pk): void
    {
        if (static::$blnWatchChanges) {
            Watcher::markTableModified (static::getDatabase()->Database, '<?= $objTable->Name ?>');
        }
    }

    /**
    * Broadcast an update notification for the specified primary key and fields.
    *
    * @param <?= $pkType ?> $pk The primary key of the record that has been updated.
    * @param array $fields An array of field names that have been updated.
    * @return void
    */
    protected static function broadcastUpdate(<?= $pkType ?> $pk, array $fields): void
    {
        if (static::$blnWatchChanges) {
            Watcher::markTableModified (static::getDatabase()->Database, '<?= $objTable->Name ?>');
        }
	}

    /**
    * Marks a table as modified and broadcasts a delete event if changes are being watched.
    *
    * @param <?= $pkType ?> $pk The primary key of the record being deleted.
    * @return void
    */
    protected static function broadcastDelete(<?= $pkType ?> $pk): void
    {
        if (static::$blnWatchChanges) {
            Watcher::markTableModified (static::getDatabase()->Database, '<?= $objTable->Name ?>');
        }
    }

    /**
    * Broadcasts the deletion of all records for the associated table.
    *
    * Marks the associated table as modified in the watcher if change tracking is enabled.
    *
    * @return void
    */
    protected static function broadcastDeleteAll(): void
    {
        if (static::$blnWatchChanges) {
            Watcher::markTableModified (static::getDatabase()->Database, '<?= $objTable->Name ?>');
        }
    }

    /**
    * Broadcasts that an association has been added to a table.
    *
    * @param string $strTableName The name of the table where the association was added.
    * @param <?= $pkType ?> $pk1 The first primary key involved in the association.
    * @param mixed $pk2 The second primary key involved in the association.
    * @return void
    */
    protected static function broadcastAssociationAdded(string $strTableName, <?= $pkType ?> $pk1, mixed $pk2): void
    {
        if (static::$blnWatchChanges) {
            Watcher::markTableModified (static::getDatabase()->Database, $strTableName);
        }
    }

    /**
    * Broadcasts the removal of an association in the specified table.
    *
    * @param string $strTableName The name of the table where the association was removed.
    * @param <?= $pkType ?>|null $pk1 The primary key of the first associated record (optional).
    * @param mixed|null $pk2 The primary key of the second associated record (optional).
    * @return void
    */
    protected static function broadcastAssociationRemoved(string $strTableName, ?<?= $pkType ?> $pk1 = null, mixed $pk2 = null): void
    {
        if (static::$blnWatchChanges) {
            Watcher::markTableModified (static::getDatabase()->Database, $strTableName);
        }
    }