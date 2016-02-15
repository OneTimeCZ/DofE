<?php

namespace Models\Base;

use \DateTime;
use \Exception;
use \PDO;
use Models\Article as ChildArticle;
use Models\ArticleQuery as ChildArticleQuery;
use Models\BugReport as ChildBugReport;
use Models\BugReportQuery as ChildBugReportQuery;
use Models\Comment as ChildComment;
use Models\CommentQuery as ChildCommentQuery;
use Models\Idea as ChildIdea;
use Models\IdeaQuery as ChildIdeaQuery;
use Models\Image as ChildImage;
use Models\ImageQuery as ChildImageQuery;
use Models\Member as ChildMember;
use Models\MemberQuery as ChildMemberQuery;
use Models\MembershipApplication as ChildMembershipApplication;
use Models\MembershipApplicationQuery as ChildMembershipApplicationQuery;
use Models\Rating as ChildRating;
use Models\RatingQuery as ChildRatingQuery;
use Models\User as ChildUser;
use Models\UserQuery as ChildUserQuery;
use Models\UserReport as ChildUserReport;
use Models\UserReportQuery as ChildUserReportQuery;
use Models\Map\UserTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

/**
 * Base class that represents a row from the 'users' table.
 *
 *
 *
* @package    propel.generator.Models.Base
*/
abstract class User implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Models\\Map\\UserTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     *
     * @var        int
     */
    protected $id;

    /**
     * The value for the id_member field.
     *
     * @var        int
     */
    protected $id_member;

    /**
     * The value for the username field.
     *
     * @var        string
     */
    protected $username;

    /**
     * The value for the member_from field.
     *
     * @var        \DateTime
     */
    protected $member_from;

    /**
     * The value for the url field.
     *
     * @var        string
     */
    protected $url;

    /**
     * The value for the email field.
     *
     * @var        string
     */
    protected $email;

    /**
     * The value for the email_confirmed_at field.
     *
     * @var        \DateTime
     */
    protected $email_confirmed_at;

    /**
     * The value for the email_confirm_token field.
     *
     * @var        string
     */
    protected $email_confirm_token;

    /**
     * The value for the password field.
     *
     * @var        string
     */
    protected $password;

    /**
     * The value for the password_reset_token field.
     *
     * @var        string
     */
    protected $password_reset_token;

    /**
     * The value for the permissions field.
     *
     * @var        int
     */
    protected $permissions;

    /**
     * The value for the signin_count field.
     *
     * @var        int
     */
    protected $signin_count;

    /**
     * The value for the id_image field.
     *
     * @var        int
     */
    protected $id_image;

    /**
     * The value for the last_signin_at field.
     *
     * @var        \DateTime
     */
    protected $last_signin_at;

    /**
     * The value for the created_at field.
     *
     * @var        \DateTime
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     *
     * @var        \DateTime
     */
    protected $updated_at;

    /**
     * @var        ChildImage
     */
    protected $aImage;

    /**
     * @var        ChildMember
     */
    protected $aMember;

    /**
     * @var        ObjectCollection|ChildArticle[] Collection to store aggregation of ChildArticle objects.
     */
    protected $collArticles;
    protected $collArticlesPartial;

    /**
     * @var        ObjectCollection|ChildComment[] Collection to store aggregation of ChildComment objects.
     */
    protected $collComments;
    protected $collCommentsPartial;

    /**
     * @var        ObjectCollection|ChildRating[] Collection to store aggregation of ChildRating objects.
     */
    protected $collRatings;
    protected $collRatingsPartial;

    /**
     * @var        ObjectCollection|ChildUserReport[] Collection to store aggregation of ChildUserReport objects.
     */
    protected $collUserReportsRelatedByIdUser;
    protected $collUserReportsRelatedByIdUserPartial;

    /**
     * @var        ObjectCollection|ChildUserReport[] Collection to store aggregation of ChildUserReport objects.
     */
    protected $collUserReportsRelatedByIdUserReported;
    protected $collUserReportsRelatedByIdUserReportedPartial;

    /**
     * @var        ObjectCollection|ChildBugReport[] Collection to store aggregation of ChildBugReport objects.
     */
    protected $collBugReports;
    protected $collBugReportsPartial;

    /**
     * @var        ObjectCollection|ChildIdea[] Collection to store aggregation of ChildIdea objects.
     */
    protected $collIdeasRelatedByIdUser;
    protected $collIdeasRelatedByIdUserPartial;

    /**
     * @var        ObjectCollection|ChildIdea[] Collection to store aggregation of ChildIdea objects.
     */
    protected $collIdeasRelatedByApprovedBy;
    protected $collIdeasRelatedByApprovedByPartial;

    /**
     * @var        ObjectCollection|ChildMembershipApplication[] Collection to store aggregation of ChildMembershipApplication objects.
     */
    protected $collMembershipApplications;
    protected $collMembershipApplicationsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildArticle[]
     */
    protected $articlesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildComment[]
     */
    protected $commentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildRating[]
     */
    protected $ratingsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildUserReport[]
     */
    protected $userReportsRelatedByIdUserScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildUserReport[]
     */
    protected $userReportsRelatedByIdUserReportedScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildBugReport[]
     */
    protected $bugReportsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildIdea[]
     */
    protected $ideasRelatedByIdUserScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildIdea[]
     */
    protected $ideasRelatedByApprovedByScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildMembershipApplication[]
     */
    protected $membershipApplicationsScheduledForDeletion = null;

    /**
     * Initializes internal state of Models\Base\User object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>User</code> instance.  If
     * <code>obj</code> is an instance of <code>User</code>, delegates to
     * <code>equals(User)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|User The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        $cls = new \ReflectionClass($this);
        $propertyNames = [];
        $serializableProperties = array_diff($cls->getProperties(), $cls->getProperties(\ReflectionProperty::IS_STATIC));

        foreach($serializableProperties as $property) {
            $propertyNames[] = $property->getName();
        }

        return $propertyNames;
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [id_member] column value.
     *
     * @return int
     */
    public function getIdMember()
    {
        return $this->id_member;
    }

    /**
     * Get the [username] column value.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the [optionally formatted] temporal [member_from] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getMemberFrom($format = NULL)
    {
        if ($format === null) {
            return $this->member_from;
        } else {
            return $this->member_from instanceof \DateTime ? $this->member_from->format($format) : null;
        }
    }

    /**
     * Get the [url] column value.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the [email] column value.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the [optionally formatted] temporal [email_confirmed_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getEmailConfirmedAt($format = NULL)
    {
        if ($format === null) {
            return $this->email_confirmed_at;
        } else {
            return $this->email_confirmed_at instanceof \DateTime ? $this->email_confirmed_at->format($format) : null;
        }
    }

    /**
     * Get the [email_confirm_token] column value.
     *
     * @return string
     */
    public function getEmailConfirmToken()
    {
        return $this->email_confirm_token;
    }

    /**
     * Get the [password] column value.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get the [password_reset_token] column value.
     *
     * @return string
     */
    public function getPasswordResetToken()
    {
        return $this->password_reset_token;
    }

    /**
     * Get the [permissions] column value.
     *
     * @return int
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Get the [signin_count] column value.
     *
     * @return int
     */
    public function getSigninCount()
    {
        return $this->signin_count;
    }

    /**
     * Get the [id_image] column value.
     *
     * @return int
     */
    public function getIdImage()
    {
        return $this->id_image;
    }

    /**
     * Get the [optionally formatted] temporal [last_signin_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getLastSigninAt($format = NULL)
    {
        if ($format === null) {
            return $this->last_signin_at;
        } else {
            return $this->last_signin_at instanceof \DateTime ? $this->last_signin_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[UserTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [id_member] column.
     *
     * @param int $v new value
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setIdMember($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id_member !== $v) {
            $this->id_member = $v;
            $this->modifiedColumns[UserTableMap::COL_ID_MEMBER] = true;
        }

        if ($this->aMember !== null && $this->aMember->getId() !== $v) {
            $this->aMember = null;
        }

        return $this;
    } // setIdMember()

    /**
     * Set the value of [username] column.
     *
     * @param string $v new value
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setUsername($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->username !== $v) {
            $this->username = $v;
            $this->modifiedColumns[UserTableMap::COL_USERNAME] = true;
        }

        return $this;
    } // setUsername()

    /**
     * Sets the value of [member_from] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setMemberFrom($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->member_from !== null || $dt !== null) {
            if ($this->member_from === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->member_from->format("Y-m-d H:i:s")) {
                $this->member_from = $dt === null ? null : clone $dt;
                $this->modifiedColumns[UserTableMap::COL_MEMBER_FROM] = true;
            }
        } // if either are not null

        return $this;
    } // setMemberFrom()

    /**
     * Set the value of [url] column.
     *
     * @param string $v new value
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->url !== $v) {
            $this->url = $v;
            $this->modifiedColumns[UserTableMap::COL_URL] = true;
        }

        return $this;
    } // setUrl()

    /**
     * Set the value of [email] column.
     *
     * @param string $v new value
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setEmail($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[UserTableMap::COL_EMAIL] = true;
        }

        return $this;
    } // setEmail()

    /**
     * Sets the value of [email_confirmed_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setEmailConfirmedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->email_confirmed_at !== null || $dt !== null) {
            if ($this->email_confirmed_at === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->email_confirmed_at->format("Y-m-d H:i:s")) {
                $this->email_confirmed_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[UserTableMap::COL_EMAIL_CONFIRMED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setEmailConfirmedAt()

    /**
     * Set the value of [email_confirm_token] column.
     *
     * @param string $v new value
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setEmailConfirmToken($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->email_confirm_token !== $v) {
            $this->email_confirm_token = $v;
            $this->modifiedColumns[UserTableMap::COL_EMAIL_CONFIRM_TOKEN] = true;
        }

        return $this;
    } // setEmailConfirmToken()

    /**
     * Set the value of [password] column.
     *
     * @param string $v new value
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setPassword($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->password !== $v) {
            $this->password = $v;
            $this->modifiedColumns[UserTableMap::COL_PASSWORD] = true;
        }

        return $this;
    } // setPassword()

    /**
     * Set the value of [password_reset_token] column.
     *
     * @param string $v new value
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setPasswordResetToken($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->password_reset_token !== $v) {
            $this->password_reset_token = $v;
            $this->modifiedColumns[UserTableMap::COL_PASSWORD_RESET_TOKEN] = true;
        }

        return $this;
    } // setPasswordResetToken()

    /**
     * Set the value of [permissions] column.
     *
     * @param int $v new value
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setPermissions($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->permissions !== $v) {
            $this->permissions = $v;
            $this->modifiedColumns[UserTableMap::COL_PERMISSIONS] = true;
        }

        return $this;
    } // setPermissions()

    /**
     * Set the value of [signin_count] column.
     *
     * @param int $v new value
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setSigninCount($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->signin_count !== $v) {
            $this->signin_count = $v;
            $this->modifiedColumns[UserTableMap::COL_SIGNIN_COUNT] = true;
        }

        return $this;
    } // setSigninCount()

    /**
     * Set the value of [id_image] column.
     *
     * @param int $v new value
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setIdImage($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id_image !== $v) {
            $this->id_image = $v;
            $this->modifiedColumns[UserTableMap::COL_ID_IMAGE] = true;
        }

        if ($this->aImage !== null && $this->aImage->getId() !== $v) {
            $this->aImage = null;
        }

        return $this;
    } // setIdImage()

    /**
     * Sets the value of [last_signin_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setLastSigninAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->last_signin_at !== null || $dt !== null) {
            if ($this->last_signin_at === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->last_signin_at->format("Y-m-d H:i:s")) {
                $this->last_signin_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[UserTableMap::COL_LAST_SIGNIN_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setLastSigninAt()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($this->created_at === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->created_at->format("Y-m-d H:i:s")) {
                $this->created_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[UserTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($this->updated_at === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->updated_at->format("Y-m-d H:i:s")) {
                $this->updated_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[UserTableMap::COL_UPDATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : UserTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : UserTableMap::translateFieldName('IdMember', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id_member = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : UserTableMap::translateFieldName('Username', TableMap::TYPE_PHPNAME, $indexType)];
            $this->username = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : UserTableMap::translateFieldName('MemberFrom', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->member_from = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : UserTableMap::translateFieldName('Url', TableMap::TYPE_PHPNAME, $indexType)];
            $this->url = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : UserTableMap::translateFieldName('Email', TableMap::TYPE_PHPNAME, $indexType)];
            $this->email = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : UserTableMap::translateFieldName('EmailConfirmedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->email_confirmed_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : UserTableMap::translateFieldName('EmailConfirmToken', TableMap::TYPE_PHPNAME, $indexType)];
            $this->email_confirm_token = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : UserTableMap::translateFieldName('Password', TableMap::TYPE_PHPNAME, $indexType)];
            $this->password = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : UserTableMap::translateFieldName('PasswordResetToken', TableMap::TYPE_PHPNAME, $indexType)];
            $this->password_reset_token = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : UserTableMap::translateFieldName('Permissions', TableMap::TYPE_PHPNAME, $indexType)];
            $this->permissions = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : UserTableMap::translateFieldName('SigninCount', TableMap::TYPE_PHPNAME, $indexType)];
            $this->signin_count = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : UserTableMap::translateFieldName('IdImage', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id_image = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : UserTableMap::translateFieldName('LastSigninAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->last_signin_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : UserTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 15 + $startcol : UserTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 16; // 16 = UserTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Models\\User'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aMember !== null && $this->id_member !== $this->aMember->getId()) {
            $this->aMember = null;
        }
        if ($this->aImage !== null && $this->id_image !== $this->aImage->getId()) {
            $this->aImage = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(UserTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildUserQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aImage = null;
            $this->aMember = null;
            $this->collArticles = null;

            $this->collComments = null;

            $this->collRatings = null;

            $this->collUserReportsRelatedByIdUser = null;

            $this->collUserReportsRelatedByIdUserReported = null;

            $this->collBugReports = null;

            $this->collIdeasRelatedByIdUser = null;

            $this->collIdeasRelatedByApprovedBy = null;

            $this->collMembershipApplications = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see User::setDeleted()
     * @see User::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildUserQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $isInsert = $this->isNew();
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior

                if (!$this->isColumnModified(UserTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(UserTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(UserTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                UserTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aImage !== null) {
                if ($this->aImage->isModified() || $this->aImage->isNew()) {
                    $affectedRows += $this->aImage->save($con);
                }
                $this->setImage($this->aImage);
            }

            if ($this->aMember !== null) {
                if ($this->aMember->isModified() || $this->aMember->isNew()) {
                    $affectedRows += $this->aMember->save($con);
                }
                $this->setMember($this->aMember);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            if ($this->articlesScheduledForDeletion !== null) {
                if (!$this->articlesScheduledForDeletion->isEmpty()) {
                    \Models\ArticleQuery::create()
                        ->filterByPrimaryKeys($this->articlesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->articlesScheduledForDeletion = null;
                }
            }

            if ($this->collArticles !== null) {
                foreach ($this->collArticles as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->commentsScheduledForDeletion !== null) {
                if (!$this->commentsScheduledForDeletion->isEmpty()) {
                    \Models\CommentQuery::create()
                        ->filterByPrimaryKeys($this->commentsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->commentsScheduledForDeletion = null;
                }
            }

            if ($this->collComments !== null) {
                foreach ($this->collComments as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ratingsScheduledForDeletion !== null) {
                if (!$this->ratingsScheduledForDeletion->isEmpty()) {
                    \Models\RatingQuery::create()
                        ->filterByPrimaryKeys($this->ratingsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ratingsScheduledForDeletion = null;
                }
            }

            if ($this->collRatings !== null) {
                foreach ($this->collRatings as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->userReportsRelatedByIdUserScheduledForDeletion !== null) {
                if (!$this->userReportsRelatedByIdUserScheduledForDeletion->isEmpty()) {
                    \Models\UserReportQuery::create()
                        ->filterByPrimaryKeys($this->userReportsRelatedByIdUserScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->userReportsRelatedByIdUserScheduledForDeletion = null;
                }
            }

            if ($this->collUserReportsRelatedByIdUser !== null) {
                foreach ($this->collUserReportsRelatedByIdUser as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->userReportsRelatedByIdUserReportedScheduledForDeletion !== null) {
                if (!$this->userReportsRelatedByIdUserReportedScheduledForDeletion->isEmpty()) {
                    \Models\UserReportQuery::create()
                        ->filterByPrimaryKeys($this->userReportsRelatedByIdUserReportedScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->userReportsRelatedByIdUserReportedScheduledForDeletion = null;
                }
            }

            if ($this->collUserReportsRelatedByIdUserReported !== null) {
                foreach ($this->collUserReportsRelatedByIdUserReported as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->bugReportsScheduledForDeletion !== null) {
                if (!$this->bugReportsScheduledForDeletion->isEmpty()) {
                    \Models\BugReportQuery::create()
                        ->filterByPrimaryKeys($this->bugReportsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->bugReportsScheduledForDeletion = null;
                }
            }

            if ($this->collBugReports !== null) {
                foreach ($this->collBugReports as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ideasRelatedByIdUserScheduledForDeletion !== null) {
                if (!$this->ideasRelatedByIdUserScheduledForDeletion->isEmpty()) {
                    \Models\IdeaQuery::create()
                        ->filterByPrimaryKeys($this->ideasRelatedByIdUserScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ideasRelatedByIdUserScheduledForDeletion = null;
                }
            }

            if ($this->collIdeasRelatedByIdUser !== null) {
                foreach ($this->collIdeasRelatedByIdUser as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ideasRelatedByApprovedByScheduledForDeletion !== null) {
                if (!$this->ideasRelatedByApprovedByScheduledForDeletion->isEmpty()) {
                    foreach ($this->ideasRelatedByApprovedByScheduledForDeletion as $ideaRelatedByApprovedBy) {
                        // need to save related object because we set the relation to null
                        $ideaRelatedByApprovedBy->save($con);
                    }
                    $this->ideasRelatedByApprovedByScheduledForDeletion = null;
                }
            }

            if ($this->collIdeasRelatedByApprovedBy !== null) {
                foreach ($this->collIdeasRelatedByApprovedBy as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->membershipApplicationsScheduledForDeletion !== null) {
                if (!$this->membershipApplicationsScheduledForDeletion->isEmpty()) {
                    \Models\MembershipApplicationQuery::create()
                        ->filterByPrimaryKeys($this->membershipApplicationsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->membershipApplicationsScheduledForDeletion = null;
                }
            }

            if ($this->collMembershipApplications !== null) {
                foreach ($this->collMembershipApplications as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[UserTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . UserTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(UserTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(UserTableMap::COL_ID_MEMBER)) {
            $modifiedColumns[':p' . $index++]  = 'id_member';
        }
        if ($this->isColumnModified(UserTableMap::COL_USERNAME)) {
            $modifiedColumns[':p' . $index++]  = 'username';
        }
        if ($this->isColumnModified(UserTableMap::COL_MEMBER_FROM)) {
            $modifiedColumns[':p' . $index++]  = 'member_from';
        }
        if ($this->isColumnModified(UserTableMap::COL_URL)) {
            $modifiedColumns[':p' . $index++]  = 'url';
        }
        if ($this->isColumnModified(UserTableMap::COL_EMAIL)) {
            $modifiedColumns[':p' . $index++]  = 'email';
        }
        if ($this->isColumnModified(UserTableMap::COL_EMAIL_CONFIRMED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'email_confirmed_at';
        }
        if ($this->isColumnModified(UserTableMap::COL_EMAIL_CONFIRM_TOKEN)) {
            $modifiedColumns[':p' . $index++]  = 'email_confirm_token';
        }
        if ($this->isColumnModified(UserTableMap::COL_PASSWORD)) {
            $modifiedColumns[':p' . $index++]  = 'password';
        }
        if ($this->isColumnModified(UserTableMap::COL_PASSWORD_RESET_TOKEN)) {
            $modifiedColumns[':p' . $index++]  = 'password_reset_token';
        }
        if ($this->isColumnModified(UserTableMap::COL_PERMISSIONS)) {
            $modifiedColumns[':p' . $index++]  = 'permissions';
        }
        if ($this->isColumnModified(UserTableMap::COL_SIGNIN_COUNT)) {
            $modifiedColumns[':p' . $index++]  = 'signin_count';
        }
        if ($this->isColumnModified(UserTableMap::COL_ID_IMAGE)) {
            $modifiedColumns[':p' . $index++]  = 'id_image';
        }
        if ($this->isColumnModified(UserTableMap::COL_LAST_SIGNIN_AT)) {
            $modifiedColumns[':p' . $index++]  = 'last_signin_at';
        }
        if ($this->isColumnModified(UserTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'created_at';
        }
        if ($this->isColumnModified(UserTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'updated_at';
        }

        $sql = sprintf(
            'INSERT INTO users (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'id':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'id_member':
                        $stmt->bindValue($identifier, $this->id_member, PDO::PARAM_INT);
                        break;
                    case 'username':
                        $stmt->bindValue($identifier, $this->username, PDO::PARAM_STR);
                        break;
                    case 'member_from':
                        $stmt->bindValue($identifier, $this->member_from ? $this->member_from->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'url':
                        $stmt->bindValue($identifier, $this->url, PDO::PARAM_STR);
                        break;
                    case 'email':
                        $stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case 'email_confirmed_at':
                        $stmt->bindValue($identifier, $this->email_confirmed_at ? $this->email_confirmed_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'email_confirm_token':
                        $stmt->bindValue($identifier, $this->email_confirm_token, PDO::PARAM_STR);
                        break;
                    case 'password':
                        $stmt->bindValue($identifier, $this->password, PDO::PARAM_STR);
                        break;
                    case 'password_reset_token':
                        $stmt->bindValue($identifier, $this->password_reset_token, PDO::PARAM_STR);
                        break;
                    case 'permissions':
                        $stmt->bindValue($identifier, $this->permissions, PDO::PARAM_INT);
                        break;
                    case 'signin_count':
                        $stmt->bindValue($identifier, $this->signin_count, PDO::PARAM_INT);
                        break;
                    case 'id_image':
                        $stmt->bindValue($identifier, $this->id_image, PDO::PARAM_INT);
                        break;
                    case 'last_signin_at':
                        $stmt->bindValue($identifier, $this->last_signin_at ? $this->last_signin_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'created_at':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'updated_at':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = UserTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getIdMember();
                break;
            case 2:
                return $this->getUsername();
                break;
            case 3:
                return $this->getMemberFrom();
                break;
            case 4:
                return $this->getUrl();
                break;
            case 5:
                return $this->getEmail();
                break;
            case 6:
                return $this->getEmailConfirmedAt();
                break;
            case 7:
                return $this->getEmailConfirmToken();
                break;
            case 8:
                return $this->getPassword();
                break;
            case 9:
                return $this->getPasswordResetToken();
                break;
            case 10:
                return $this->getPermissions();
                break;
            case 11:
                return $this->getSigninCount();
                break;
            case 12:
                return $this->getIdImage();
                break;
            case 13:
                return $this->getLastSigninAt();
                break;
            case 14:
                return $this->getCreatedAt();
                break;
            case 15:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        if (isset($alreadyDumpedObjects['User'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['User'][$this->hashCode()] = true;
        $keys = UserTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getIdMember(),
            $keys[2] => $this->getUsername(),
            $keys[3] => $this->getMemberFrom(),
            $keys[4] => $this->getUrl(),
            $keys[5] => $this->getEmail(),
            $keys[6] => $this->getEmailConfirmedAt(),
            $keys[7] => $this->getEmailConfirmToken(),
            $keys[8] => $this->getPassword(),
            $keys[9] => $this->getPasswordResetToken(),
            $keys[10] => $this->getPermissions(),
            $keys[11] => $this->getSigninCount(),
            $keys[12] => $this->getIdImage(),
            $keys[13] => $this->getLastSigninAt(),
            $keys[14] => $this->getCreatedAt(),
            $keys[15] => $this->getUpdatedAt(),
        );
        if ($result[$keys[3]] instanceof \DateTime) {
            $result[$keys[3]] = $result[$keys[3]]->format('c');
        }

        if ($result[$keys[6]] instanceof \DateTime) {
            $result[$keys[6]] = $result[$keys[6]]->format('c');
        }

        if ($result[$keys[13]] instanceof \DateTime) {
            $result[$keys[13]] = $result[$keys[13]]->format('c');
        }

        if ($result[$keys[14]] instanceof \DateTime) {
            $result[$keys[14]] = $result[$keys[14]]->format('c');
        }

        if ($result[$keys[15]] instanceof \DateTime) {
            $result[$keys[15]] = $result[$keys[15]]->format('c');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aImage) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'image';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'images';
                        break;
                    default:
                        $key = 'Image';
                }

                $result[$key] = $this->aImage->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aMember) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'member';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'members';
                        break;
                    default:
                        $key = 'Member';
                }

                $result[$key] = $this->aMember->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collArticles) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'articles';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'articless';
                        break;
                    default:
                        $key = 'Articles';
                }

                $result[$key] = $this->collArticles->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collComments) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'comments';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'commentss';
                        break;
                    default:
                        $key = 'Comments';
                }

                $result[$key] = $this->collComments->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collRatings) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'ratings';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'ratingss';
                        break;
                    default:
                        $key = 'Ratings';
                }

                $result[$key] = $this->collRatings->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUserReportsRelatedByIdUser) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'userReports';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'user_reportss';
                        break;
                    default:
                        $key = 'UserReports';
                }

                $result[$key] = $this->collUserReportsRelatedByIdUser->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUserReportsRelatedByIdUserReported) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'userReports';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'user_reportss';
                        break;
                    default:
                        $key = 'UserReports';
                }

                $result[$key] = $this->collUserReportsRelatedByIdUserReported->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collBugReports) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'bugReports';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'bug_reportss';
                        break;
                    default:
                        $key = 'BugReports';
                }

                $result[$key] = $this->collBugReports->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collIdeasRelatedByIdUser) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'ideas';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'ideass';
                        break;
                    default:
                        $key = 'Ideas';
                }

                $result[$key] = $this->collIdeasRelatedByIdUser->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collIdeasRelatedByApprovedBy) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'ideas';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'ideass';
                        break;
                    default:
                        $key = 'Ideas';
                }

                $result[$key] = $this->collIdeasRelatedByApprovedBy->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collMembershipApplications) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'membershipApplications';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'membership_applicationss';
                        break;
                    default:
                        $key = 'MembershipApplications';
                }

                $result[$key] = $this->collMembershipApplications->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\Models\User
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = UserTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Models\User
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setIdMember($value);
                break;
            case 2:
                $this->setUsername($value);
                break;
            case 3:
                $this->setMemberFrom($value);
                break;
            case 4:
                $this->setUrl($value);
                break;
            case 5:
                $this->setEmail($value);
                break;
            case 6:
                $this->setEmailConfirmedAt($value);
                break;
            case 7:
                $this->setEmailConfirmToken($value);
                break;
            case 8:
                $this->setPassword($value);
                break;
            case 9:
                $this->setPasswordResetToken($value);
                break;
            case 10:
                $this->setPermissions($value);
                break;
            case 11:
                $this->setSigninCount($value);
                break;
            case 12:
                $this->setIdImage($value);
                break;
            case 13:
                $this->setLastSigninAt($value);
                break;
            case 14:
                $this->setCreatedAt($value);
                break;
            case 15:
                $this->setUpdatedAt($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = UserTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setIdMember($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setUsername($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setMemberFrom($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setUrl($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setEmail($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setEmailConfirmedAt($arr[$keys[6]]);
        }
        if (array_key_exists($keys[7], $arr)) {
            $this->setEmailConfirmToken($arr[$keys[7]]);
        }
        if (array_key_exists($keys[8], $arr)) {
            $this->setPassword($arr[$keys[8]]);
        }
        if (array_key_exists($keys[9], $arr)) {
            $this->setPasswordResetToken($arr[$keys[9]]);
        }
        if (array_key_exists($keys[10], $arr)) {
            $this->setPermissions($arr[$keys[10]]);
        }
        if (array_key_exists($keys[11], $arr)) {
            $this->setSigninCount($arr[$keys[11]]);
        }
        if (array_key_exists($keys[12], $arr)) {
            $this->setIdImage($arr[$keys[12]]);
        }
        if (array_key_exists($keys[13], $arr)) {
            $this->setLastSigninAt($arr[$keys[13]]);
        }
        if (array_key_exists($keys[14], $arr)) {
            $this->setCreatedAt($arr[$keys[14]]);
        }
        if (array_key_exists($keys[15], $arr)) {
            $this->setUpdatedAt($arr[$keys[15]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this|\Models\User The current object, for fluid interface
     */
    public function importFrom($parser, $data, $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(UserTableMap::DATABASE_NAME);

        if ($this->isColumnModified(UserTableMap::COL_ID)) {
            $criteria->add(UserTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(UserTableMap::COL_ID_MEMBER)) {
            $criteria->add(UserTableMap::COL_ID_MEMBER, $this->id_member);
        }
        if ($this->isColumnModified(UserTableMap::COL_USERNAME)) {
            $criteria->add(UserTableMap::COL_USERNAME, $this->username);
        }
        if ($this->isColumnModified(UserTableMap::COL_MEMBER_FROM)) {
            $criteria->add(UserTableMap::COL_MEMBER_FROM, $this->member_from);
        }
        if ($this->isColumnModified(UserTableMap::COL_URL)) {
            $criteria->add(UserTableMap::COL_URL, $this->url);
        }
        if ($this->isColumnModified(UserTableMap::COL_EMAIL)) {
            $criteria->add(UserTableMap::COL_EMAIL, $this->email);
        }
        if ($this->isColumnModified(UserTableMap::COL_EMAIL_CONFIRMED_AT)) {
            $criteria->add(UserTableMap::COL_EMAIL_CONFIRMED_AT, $this->email_confirmed_at);
        }
        if ($this->isColumnModified(UserTableMap::COL_EMAIL_CONFIRM_TOKEN)) {
            $criteria->add(UserTableMap::COL_EMAIL_CONFIRM_TOKEN, $this->email_confirm_token);
        }
        if ($this->isColumnModified(UserTableMap::COL_PASSWORD)) {
            $criteria->add(UserTableMap::COL_PASSWORD, $this->password);
        }
        if ($this->isColumnModified(UserTableMap::COL_PASSWORD_RESET_TOKEN)) {
            $criteria->add(UserTableMap::COL_PASSWORD_RESET_TOKEN, $this->password_reset_token);
        }
        if ($this->isColumnModified(UserTableMap::COL_PERMISSIONS)) {
            $criteria->add(UserTableMap::COL_PERMISSIONS, $this->permissions);
        }
        if ($this->isColumnModified(UserTableMap::COL_SIGNIN_COUNT)) {
            $criteria->add(UserTableMap::COL_SIGNIN_COUNT, $this->signin_count);
        }
        if ($this->isColumnModified(UserTableMap::COL_ID_IMAGE)) {
            $criteria->add(UserTableMap::COL_ID_IMAGE, $this->id_image);
        }
        if ($this->isColumnModified(UserTableMap::COL_LAST_SIGNIN_AT)) {
            $criteria->add(UserTableMap::COL_LAST_SIGNIN_AT, $this->last_signin_at);
        }
        if ($this->isColumnModified(UserTableMap::COL_CREATED_AT)) {
            $criteria->add(UserTableMap::COL_CREATED_AT, $this->created_at);
        }
        if ($this->isColumnModified(UserTableMap::COL_UPDATED_AT)) {
            $criteria->add(UserTableMap::COL_UPDATED_AT, $this->updated_at);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildUserQuery::create();
        $criteria->add(UserTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Models\User (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setIdMember($this->getIdMember());
        $copyObj->setUsername($this->getUsername());
        $copyObj->setMemberFrom($this->getMemberFrom());
        $copyObj->setUrl($this->getUrl());
        $copyObj->setEmail($this->getEmail());
        $copyObj->setEmailConfirmedAt($this->getEmailConfirmedAt());
        $copyObj->setEmailConfirmToken($this->getEmailConfirmToken());
        $copyObj->setPassword($this->getPassword());
        $copyObj->setPasswordResetToken($this->getPasswordResetToken());
        $copyObj->setPermissions($this->getPermissions());
        $copyObj->setSigninCount($this->getSigninCount());
        $copyObj->setIdImage($this->getIdImage());
        $copyObj->setLastSigninAt($this->getLastSigninAt());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getArticles() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addArticle($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getComments() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addComment($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getRatings() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addRating($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUserReportsRelatedByIdUser() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserReportRelatedByIdUser($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUserReportsRelatedByIdUserReported() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserReportRelatedByIdUserReported($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getBugReports() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addBugReport($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getIdeasRelatedByIdUser() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addIdeaRelatedByIdUser($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getIdeasRelatedByApprovedBy() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addIdeaRelatedByApprovedBy($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getMembershipApplications() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addMembershipApplication($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \Models\User Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildImage object.
     *
     * @param  ChildImage $v
     * @return $this|\Models\User The current object (for fluent API support)
     * @throws PropelException
     */
    public function setImage(ChildImage $v = null)
    {
        if ($v === null) {
            $this->setIdImage(NULL);
        } else {
            $this->setIdImage($v->getId());
        }

        $this->aImage = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildImage object, it will not be re-added.
        if ($v !== null) {
            $v->addUser($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildImage object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildImage The associated ChildImage object.
     * @throws PropelException
     */
    public function getImage(ConnectionInterface $con = null)
    {
        if ($this->aImage === null && ($this->id_image !== null)) {
            $this->aImage = ChildImageQuery::create()->findPk($this->id_image, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aImage->addUsers($this);
             */
        }

        return $this->aImage;
    }

    /**
     * Declares an association between this object and a ChildMember object.
     *
     * @param  ChildMember $v
     * @return $this|\Models\User The current object (for fluent API support)
     * @throws PropelException
     */
    public function setMember(ChildMember $v = null)
    {
        if ($v === null) {
            $this->setIdMember(NULL);
        } else {
            $this->setIdMember($v->getId());
        }

        $this->aMember = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildMember object, it will not be re-added.
        if ($v !== null) {
            $v->addUser($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildMember object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildMember The associated ChildMember object.
     * @throws PropelException
     */
    public function getMember(ConnectionInterface $con = null)
    {
        if ($this->aMember === null && ($this->id_member !== null)) {
            $this->aMember = ChildMemberQuery::create()->findPk($this->id_member, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aMember->addUsers($this);
             */
        }

        return $this->aMember;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('Article' == $relationName) {
            return $this->initArticles();
        }
        if ('Comment' == $relationName) {
            return $this->initComments();
        }
        if ('Rating' == $relationName) {
            return $this->initRatings();
        }
        if ('UserReportRelatedByIdUser' == $relationName) {
            return $this->initUserReportsRelatedByIdUser();
        }
        if ('UserReportRelatedByIdUserReported' == $relationName) {
            return $this->initUserReportsRelatedByIdUserReported();
        }
        if ('BugReport' == $relationName) {
            return $this->initBugReports();
        }
        if ('IdeaRelatedByIdUser' == $relationName) {
            return $this->initIdeasRelatedByIdUser();
        }
        if ('IdeaRelatedByApprovedBy' == $relationName) {
            return $this->initIdeasRelatedByApprovedBy();
        }
        if ('MembershipApplication' == $relationName) {
            return $this->initMembershipApplications();
        }
    }

    /**
     * Clears out the collArticles collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addArticles()
     */
    public function clearArticles()
    {
        $this->collArticles = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collArticles collection loaded partially.
     */
    public function resetPartialArticles($v = true)
    {
        $this->collArticlesPartial = $v;
    }

    /**
     * Initializes the collArticles collection.
     *
     * By default this just sets the collArticles collection to an empty array (like clearcollArticles());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initArticles($overrideExisting = true)
    {
        if (null !== $this->collArticles && !$overrideExisting) {
            return;
        }
        $this->collArticles = new ObjectCollection();
        $this->collArticles->setModel('\Models\Article');
    }

    /**
     * Gets an array of ChildArticle objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildArticle[] List of ChildArticle objects
     * @throws PropelException
     */
    public function getArticles(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collArticlesPartial && !$this->isNew();
        if (null === $this->collArticles || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collArticles) {
                // return empty collection
                $this->initArticles();
            } else {
                $collArticles = ChildArticleQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collArticlesPartial && count($collArticles)) {
                        $this->initArticles(false);

                        foreach ($collArticles as $obj) {
                            if (false == $this->collArticles->contains($obj)) {
                                $this->collArticles->append($obj);
                            }
                        }

                        $this->collArticlesPartial = true;
                    }

                    return $collArticles;
                }

                if ($partial && $this->collArticles) {
                    foreach ($this->collArticles as $obj) {
                        if ($obj->isNew()) {
                            $collArticles[] = $obj;
                        }
                    }
                }

                $this->collArticles = $collArticles;
                $this->collArticlesPartial = false;
            }
        }

        return $this->collArticles;
    }

    /**
     * Sets a collection of ChildArticle objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $articles A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function setArticles(Collection $articles, ConnectionInterface $con = null)
    {
        /** @var ChildArticle[] $articlesToDelete */
        $articlesToDelete = $this->getArticles(new Criteria(), $con)->diff($articles);


        $this->articlesScheduledForDeletion = $articlesToDelete;

        foreach ($articlesToDelete as $articleRemoved) {
            $articleRemoved->setUser(null);
        }

        $this->collArticles = null;
        foreach ($articles as $article) {
            $this->addArticle($article);
        }

        $this->collArticles = $articles;
        $this->collArticlesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Article objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Article objects.
     * @throws PropelException
     */
    public function countArticles(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collArticlesPartial && !$this->isNew();
        if (null === $this->collArticles || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collArticles) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getArticles());
            }

            $query = ChildArticleQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collArticles);
    }

    /**
     * Method called to associate a ChildArticle object to this object
     * through the ChildArticle foreign key attribute.
     *
     * @param  ChildArticle $l ChildArticle
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function addArticle(ChildArticle $l)
    {
        if ($this->collArticles === null) {
            $this->initArticles();
            $this->collArticlesPartial = true;
        }

        if (!$this->collArticles->contains($l)) {
            $this->doAddArticle($l);

            if ($this->articlesScheduledForDeletion and $this->articlesScheduledForDeletion->contains($l)) {
                $this->articlesScheduledForDeletion->remove($this->articlesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildArticle $article The ChildArticle object to add.
     */
    protected function doAddArticle(ChildArticle $article)
    {
        $this->collArticles[]= $article;
        $article->setUser($this);
    }

    /**
     * @param  ChildArticle $article The ChildArticle object to remove.
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function removeArticle(ChildArticle $article)
    {
        if ($this->getArticles()->contains($article)) {
            $pos = $this->collArticles->search($article);
            $this->collArticles->remove($pos);
            if (null === $this->articlesScheduledForDeletion) {
                $this->articlesScheduledForDeletion = clone $this->collArticles;
                $this->articlesScheduledForDeletion->clear();
            }
            $this->articlesScheduledForDeletion[]= clone $article;
            $article->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related Articles from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildArticle[] List of ChildArticle objects
     */
    public function getArticlesJoinImage(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildArticleQuery::create(null, $criteria);
        $query->joinWith('Image', $joinBehavior);

        return $this->getArticles($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related Articles from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildArticle[] List of ChildArticle objects
     */
    public function getArticlesJoinCategory(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildArticleQuery::create(null, $criteria);
        $query->joinWith('Category', $joinBehavior);

        return $this->getArticles($query, $con);
    }

    /**
     * Clears out the collComments collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addComments()
     */
    public function clearComments()
    {
        $this->collComments = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collComments collection loaded partially.
     */
    public function resetPartialComments($v = true)
    {
        $this->collCommentsPartial = $v;
    }

    /**
     * Initializes the collComments collection.
     *
     * By default this just sets the collComments collection to an empty array (like clearcollComments());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initComments($overrideExisting = true)
    {
        if (null !== $this->collComments && !$overrideExisting) {
            return;
        }
        $this->collComments = new ObjectCollection();
        $this->collComments->setModel('\Models\Comment');
    }

    /**
     * Gets an array of ChildComment objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildComment[] List of ChildComment objects
     * @throws PropelException
     */
    public function getComments(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCommentsPartial && !$this->isNew();
        if (null === $this->collComments || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collComments) {
                // return empty collection
                $this->initComments();
            } else {
                $collComments = ChildCommentQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCommentsPartial && count($collComments)) {
                        $this->initComments(false);

                        foreach ($collComments as $obj) {
                            if (false == $this->collComments->contains($obj)) {
                                $this->collComments->append($obj);
                            }
                        }

                        $this->collCommentsPartial = true;
                    }

                    return $collComments;
                }

                if ($partial && $this->collComments) {
                    foreach ($this->collComments as $obj) {
                        if ($obj->isNew()) {
                            $collComments[] = $obj;
                        }
                    }
                }

                $this->collComments = $collComments;
                $this->collCommentsPartial = false;
            }
        }

        return $this->collComments;
    }

    /**
     * Sets a collection of ChildComment objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $comments A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function setComments(Collection $comments, ConnectionInterface $con = null)
    {
        /** @var ChildComment[] $commentsToDelete */
        $commentsToDelete = $this->getComments(new Criteria(), $con)->diff($comments);


        $this->commentsScheduledForDeletion = $commentsToDelete;

        foreach ($commentsToDelete as $commentRemoved) {
            $commentRemoved->setUser(null);
        }

        $this->collComments = null;
        foreach ($comments as $comment) {
            $this->addComment($comment);
        }

        $this->collComments = $comments;
        $this->collCommentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Comment objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Comment objects.
     * @throws PropelException
     */
    public function countComments(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCommentsPartial && !$this->isNew();
        if (null === $this->collComments || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collComments) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getComments());
            }

            $query = ChildCommentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collComments);
    }

    /**
     * Method called to associate a ChildComment object to this object
     * through the ChildComment foreign key attribute.
     *
     * @param  ChildComment $l ChildComment
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function addComment(ChildComment $l)
    {
        if ($this->collComments === null) {
            $this->initComments();
            $this->collCommentsPartial = true;
        }

        if (!$this->collComments->contains($l)) {
            $this->doAddComment($l);

            if ($this->commentsScheduledForDeletion and $this->commentsScheduledForDeletion->contains($l)) {
                $this->commentsScheduledForDeletion->remove($this->commentsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildComment $comment The ChildComment object to add.
     */
    protected function doAddComment(ChildComment $comment)
    {
        $this->collComments[]= $comment;
        $comment->setUser($this);
    }

    /**
     * @param  ChildComment $comment The ChildComment object to remove.
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function removeComment(ChildComment $comment)
    {
        if ($this->getComments()->contains($comment)) {
            $pos = $this->collComments->search($comment);
            $this->collComments->remove($pos);
            if (null === $this->commentsScheduledForDeletion) {
                $this->commentsScheduledForDeletion = clone $this->collComments;
                $this->commentsScheduledForDeletion->clear();
            }
            $this->commentsScheduledForDeletion[]= clone $comment;
            $comment->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related Comments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildComment[] List of ChildComment objects
     */
    public function getCommentsJoinArticle(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCommentQuery::create(null, $criteria);
        $query->joinWith('Article', $joinBehavior);

        return $this->getComments($query, $con);
    }

    /**
     * Clears out the collRatings collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addRatings()
     */
    public function clearRatings()
    {
        $this->collRatings = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collRatings collection loaded partially.
     */
    public function resetPartialRatings($v = true)
    {
        $this->collRatingsPartial = $v;
    }

    /**
     * Initializes the collRatings collection.
     *
     * By default this just sets the collRatings collection to an empty array (like clearcollRatings());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initRatings($overrideExisting = true)
    {
        if (null !== $this->collRatings && !$overrideExisting) {
            return;
        }
        $this->collRatings = new ObjectCollection();
        $this->collRatings->setModel('\Models\Rating');
    }

    /**
     * Gets an array of ChildRating objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildRating[] List of ChildRating objects
     * @throws PropelException
     */
    public function getRatings(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collRatingsPartial && !$this->isNew();
        if (null === $this->collRatings || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collRatings) {
                // return empty collection
                $this->initRatings();
            } else {
                $collRatings = ChildRatingQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collRatingsPartial && count($collRatings)) {
                        $this->initRatings(false);

                        foreach ($collRatings as $obj) {
                            if (false == $this->collRatings->contains($obj)) {
                                $this->collRatings->append($obj);
                            }
                        }

                        $this->collRatingsPartial = true;
                    }

                    return $collRatings;
                }

                if ($partial && $this->collRatings) {
                    foreach ($this->collRatings as $obj) {
                        if ($obj->isNew()) {
                            $collRatings[] = $obj;
                        }
                    }
                }

                $this->collRatings = $collRatings;
                $this->collRatingsPartial = false;
            }
        }

        return $this->collRatings;
    }

    /**
     * Sets a collection of ChildRating objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $ratings A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function setRatings(Collection $ratings, ConnectionInterface $con = null)
    {
        /** @var ChildRating[] $ratingsToDelete */
        $ratingsToDelete = $this->getRatings(new Criteria(), $con)->diff($ratings);


        $this->ratingsScheduledForDeletion = $ratingsToDelete;

        foreach ($ratingsToDelete as $ratingRemoved) {
            $ratingRemoved->setUser(null);
        }

        $this->collRatings = null;
        foreach ($ratings as $rating) {
            $this->addRating($rating);
        }

        $this->collRatings = $ratings;
        $this->collRatingsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Rating objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Rating objects.
     * @throws PropelException
     */
    public function countRatings(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collRatingsPartial && !$this->isNew();
        if (null === $this->collRatings || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collRatings) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getRatings());
            }

            $query = ChildRatingQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collRatings);
    }

    /**
     * Method called to associate a ChildRating object to this object
     * through the ChildRating foreign key attribute.
     *
     * @param  ChildRating $l ChildRating
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function addRating(ChildRating $l)
    {
        if ($this->collRatings === null) {
            $this->initRatings();
            $this->collRatingsPartial = true;
        }

        if (!$this->collRatings->contains($l)) {
            $this->doAddRating($l);

            if ($this->ratingsScheduledForDeletion and $this->ratingsScheduledForDeletion->contains($l)) {
                $this->ratingsScheduledForDeletion->remove($this->ratingsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildRating $rating The ChildRating object to add.
     */
    protected function doAddRating(ChildRating $rating)
    {
        $this->collRatings[]= $rating;
        $rating->setUser($this);
    }

    /**
     * @param  ChildRating $rating The ChildRating object to remove.
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function removeRating(ChildRating $rating)
    {
        if ($this->getRatings()->contains($rating)) {
            $pos = $this->collRatings->search($rating);
            $this->collRatings->remove($pos);
            if (null === $this->ratingsScheduledForDeletion) {
                $this->ratingsScheduledForDeletion = clone $this->collRatings;
                $this->ratingsScheduledForDeletion->clear();
            }
            $this->ratingsScheduledForDeletion[]= clone $rating;
            $rating->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related Ratings from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildRating[] List of ChildRating objects
     */
    public function getRatingsJoinComment(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildRatingQuery::create(null, $criteria);
        $query->joinWith('Comment', $joinBehavior);

        return $this->getRatings($query, $con);
    }

    /**
     * Clears out the collUserReportsRelatedByIdUser collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addUserReportsRelatedByIdUser()
     */
    public function clearUserReportsRelatedByIdUser()
    {
        $this->collUserReportsRelatedByIdUser = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collUserReportsRelatedByIdUser collection loaded partially.
     */
    public function resetPartialUserReportsRelatedByIdUser($v = true)
    {
        $this->collUserReportsRelatedByIdUserPartial = $v;
    }

    /**
     * Initializes the collUserReportsRelatedByIdUser collection.
     *
     * By default this just sets the collUserReportsRelatedByIdUser collection to an empty array (like clearcollUserReportsRelatedByIdUser());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserReportsRelatedByIdUser($overrideExisting = true)
    {
        if (null !== $this->collUserReportsRelatedByIdUser && !$overrideExisting) {
            return;
        }
        $this->collUserReportsRelatedByIdUser = new ObjectCollection();
        $this->collUserReportsRelatedByIdUser->setModel('\Models\UserReport');
    }

    /**
     * Gets an array of ChildUserReport objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildUserReport[] List of ChildUserReport objects
     * @throws PropelException
     */
    public function getUserReportsRelatedByIdUser(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collUserReportsRelatedByIdUserPartial && !$this->isNew();
        if (null === $this->collUserReportsRelatedByIdUser || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUserReportsRelatedByIdUser) {
                // return empty collection
                $this->initUserReportsRelatedByIdUser();
            } else {
                $collUserReportsRelatedByIdUser = ChildUserReportQuery::create(null, $criteria)
                    ->filterByUserAuthor($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collUserReportsRelatedByIdUserPartial && count($collUserReportsRelatedByIdUser)) {
                        $this->initUserReportsRelatedByIdUser(false);

                        foreach ($collUserReportsRelatedByIdUser as $obj) {
                            if (false == $this->collUserReportsRelatedByIdUser->contains($obj)) {
                                $this->collUserReportsRelatedByIdUser->append($obj);
                            }
                        }

                        $this->collUserReportsRelatedByIdUserPartial = true;
                    }

                    return $collUserReportsRelatedByIdUser;
                }

                if ($partial && $this->collUserReportsRelatedByIdUser) {
                    foreach ($this->collUserReportsRelatedByIdUser as $obj) {
                        if ($obj->isNew()) {
                            $collUserReportsRelatedByIdUser[] = $obj;
                        }
                    }
                }

                $this->collUserReportsRelatedByIdUser = $collUserReportsRelatedByIdUser;
                $this->collUserReportsRelatedByIdUserPartial = false;
            }
        }

        return $this->collUserReportsRelatedByIdUser;
    }

    /**
     * Sets a collection of ChildUserReport objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $userReportsRelatedByIdUser A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function setUserReportsRelatedByIdUser(Collection $userReportsRelatedByIdUser, ConnectionInterface $con = null)
    {
        /** @var ChildUserReport[] $userReportsRelatedByIdUserToDelete */
        $userReportsRelatedByIdUserToDelete = $this->getUserReportsRelatedByIdUser(new Criteria(), $con)->diff($userReportsRelatedByIdUser);


        $this->userReportsRelatedByIdUserScheduledForDeletion = $userReportsRelatedByIdUserToDelete;

        foreach ($userReportsRelatedByIdUserToDelete as $userReportRelatedByIdUserRemoved) {
            $userReportRelatedByIdUserRemoved->setUserAuthor(null);
        }

        $this->collUserReportsRelatedByIdUser = null;
        foreach ($userReportsRelatedByIdUser as $userReportRelatedByIdUser) {
            $this->addUserReportRelatedByIdUser($userReportRelatedByIdUser);
        }

        $this->collUserReportsRelatedByIdUser = $userReportsRelatedByIdUser;
        $this->collUserReportsRelatedByIdUserPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserReport objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related UserReport objects.
     * @throws PropelException
     */
    public function countUserReportsRelatedByIdUser(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collUserReportsRelatedByIdUserPartial && !$this->isNew();
        if (null === $this->collUserReportsRelatedByIdUser || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserReportsRelatedByIdUser) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserReportsRelatedByIdUser());
            }

            $query = ChildUserReportQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUserAuthor($this)
                ->count($con);
        }

        return count($this->collUserReportsRelatedByIdUser);
    }

    /**
     * Method called to associate a ChildUserReport object to this object
     * through the ChildUserReport foreign key attribute.
     *
     * @param  ChildUserReport $l ChildUserReport
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function addUserReportRelatedByIdUser(ChildUserReport $l)
    {
        if ($this->collUserReportsRelatedByIdUser === null) {
            $this->initUserReportsRelatedByIdUser();
            $this->collUserReportsRelatedByIdUserPartial = true;
        }

        if (!$this->collUserReportsRelatedByIdUser->contains($l)) {
            $this->doAddUserReportRelatedByIdUser($l);

            if ($this->userReportsRelatedByIdUserScheduledForDeletion and $this->userReportsRelatedByIdUserScheduledForDeletion->contains($l)) {
                $this->userReportsRelatedByIdUserScheduledForDeletion->remove($this->userReportsRelatedByIdUserScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildUserReport $userReportRelatedByIdUser The ChildUserReport object to add.
     */
    protected function doAddUserReportRelatedByIdUser(ChildUserReport $userReportRelatedByIdUser)
    {
        $this->collUserReportsRelatedByIdUser[]= $userReportRelatedByIdUser;
        $userReportRelatedByIdUser->setUserAuthor($this);
    }

    /**
     * @param  ChildUserReport $userReportRelatedByIdUser The ChildUserReport object to remove.
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function removeUserReportRelatedByIdUser(ChildUserReport $userReportRelatedByIdUser)
    {
        if ($this->getUserReportsRelatedByIdUser()->contains($userReportRelatedByIdUser)) {
            $pos = $this->collUserReportsRelatedByIdUser->search($userReportRelatedByIdUser);
            $this->collUserReportsRelatedByIdUser->remove($pos);
            if (null === $this->userReportsRelatedByIdUserScheduledForDeletion) {
                $this->userReportsRelatedByIdUserScheduledForDeletion = clone $this->collUserReportsRelatedByIdUser;
                $this->userReportsRelatedByIdUserScheduledForDeletion->clear();
            }
            $this->userReportsRelatedByIdUserScheduledForDeletion[]= clone $userReportRelatedByIdUser;
            $userReportRelatedByIdUser->setUserAuthor(null);
        }

        return $this;
    }

    /**
     * Clears out the collUserReportsRelatedByIdUserReported collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addUserReportsRelatedByIdUserReported()
     */
    public function clearUserReportsRelatedByIdUserReported()
    {
        $this->collUserReportsRelatedByIdUserReported = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collUserReportsRelatedByIdUserReported collection loaded partially.
     */
    public function resetPartialUserReportsRelatedByIdUserReported($v = true)
    {
        $this->collUserReportsRelatedByIdUserReportedPartial = $v;
    }

    /**
     * Initializes the collUserReportsRelatedByIdUserReported collection.
     *
     * By default this just sets the collUserReportsRelatedByIdUserReported collection to an empty array (like clearcollUserReportsRelatedByIdUserReported());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserReportsRelatedByIdUserReported($overrideExisting = true)
    {
        if (null !== $this->collUserReportsRelatedByIdUserReported && !$overrideExisting) {
            return;
        }
        $this->collUserReportsRelatedByIdUserReported = new ObjectCollection();
        $this->collUserReportsRelatedByIdUserReported->setModel('\Models\UserReport');
    }

    /**
     * Gets an array of ChildUserReport objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildUserReport[] List of ChildUserReport objects
     * @throws PropelException
     */
    public function getUserReportsRelatedByIdUserReported(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collUserReportsRelatedByIdUserReportedPartial && !$this->isNew();
        if (null === $this->collUserReportsRelatedByIdUserReported || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUserReportsRelatedByIdUserReported) {
                // return empty collection
                $this->initUserReportsRelatedByIdUserReported();
            } else {
                $collUserReportsRelatedByIdUserReported = ChildUserReportQuery::create(null, $criteria)
                    ->filterByUserReported($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collUserReportsRelatedByIdUserReportedPartial && count($collUserReportsRelatedByIdUserReported)) {
                        $this->initUserReportsRelatedByIdUserReported(false);

                        foreach ($collUserReportsRelatedByIdUserReported as $obj) {
                            if (false == $this->collUserReportsRelatedByIdUserReported->contains($obj)) {
                                $this->collUserReportsRelatedByIdUserReported->append($obj);
                            }
                        }

                        $this->collUserReportsRelatedByIdUserReportedPartial = true;
                    }

                    return $collUserReportsRelatedByIdUserReported;
                }

                if ($partial && $this->collUserReportsRelatedByIdUserReported) {
                    foreach ($this->collUserReportsRelatedByIdUserReported as $obj) {
                        if ($obj->isNew()) {
                            $collUserReportsRelatedByIdUserReported[] = $obj;
                        }
                    }
                }

                $this->collUserReportsRelatedByIdUserReported = $collUserReportsRelatedByIdUserReported;
                $this->collUserReportsRelatedByIdUserReportedPartial = false;
            }
        }

        return $this->collUserReportsRelatedByIdUserReported;
    }

    /**
     * Sets a collection of ChildUserReport objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $userReportsRelatedByIdUserReported A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function setUserReportsRelatedByIdUserReported(Collection $userReportsRelatedByIdUserReported, ConnectionInterface $con = null)
    {
        /** @var ChildUserReport[] $userReportsRelatedByIdUserReportedToDelete */
        $userReportsRelatedByIdUserReportedToDelete = $this->getUserReportsRelatedByIdUserReported(new Criteria(), $con)->diff($userReportsRelatedByIdUserReported);


        $this->userReportsRelatedByIdUserReportedScheduledForDeletion = $userReportsRelatedByIdUserReportedToDelete;

        foreach ($userReportsRelatedByIdUserReportedToDelete as $userReportRelatedByIdUserReportedRemoved) {
            $userReportRelatedByIdUserReportedRemoved->setUserReported(null);
        }

        $this->collUserReportsRelatedByIdUserReported = null;
        foreach ($userReportsRelatedByIdUserReported as $userReportRelatedByIdUserReported) {
            $this->addUserReportRelatedByIdUserReported($userReportRelatedByIdUserReported);
        }

        $this->collUserReportsRelatedByIdUserReported = $userReportsRelatedByIdUserReported;
        $this->collUserReportsRelatedByIdUserReportedPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserReport objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related UserReport objects.
     * @throws PropelException
     */
    public function countUserReportsRelatedByIdUserReported(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collUserReportsRelatedByIdUserReportedPartial && !$this->isNew();
        if (null === $this->collUserReportsRelatedByIdUserReported || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserReportsRelatedByIdUserReported) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserReportsRelatedByIdUserReported());
            }

            $query = ChildUserReportQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUserReported($this)
                ->count($con);
        }

        return count($this->collUserReportsRelatedByIdUserReported);
    }

    /**
     * Method called to associate a ChildUserReport object to this object
     * through the ChildUserReport foreign key attribute.
     *
     * @param  ChildUserReport $l ChildUserReport
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function addUserReportRelatedByIdUserReported(ChildUserReport $l)
    {
        if ($this->collUserReportsRelatedByIdUserReported === null) {
            $this->initUserReportsRelatedByIdUserReported();
            $this->collUserReportsRelatedByIdUserReportedPartial = true;
        }

        if (!$this->collUserReportsRelatedByIdUserReported->contains($l)) {
            $this->doAddUserReportRelatedByIdUserReported($l);

            if ($this->userReportsRelatedByIdUserReportedScheduledForDeletion and $this->userReportsRelatedByIdUserReportedScheduledForDeletion->contains($l)) {
                $this->userReportsRelatedByIdUserReportedScheduledForDeletion->remove($this->userReportsRelatedByIdUserReportedScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildUserReport $userReportRelatedByIdUserReported The ChildUserReport object to add.
     */
    protected function doAddUserReportRelatedByIdUserReported(ChildUserReport $userReportRelatedByIdUserReported)
    {
        $this->collUserReportsRelatedByIdUserReported[]= $userReportRelatedByIdUserReported;
        $userReportRelatedByIdUserReported->setUserReported($this);
    }

    /**
     * @param  ChildUserReport $userReportRelatedByIdUserReported The ChildUserReport object to remove.
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function removeUserReportRelatedByIdUserReported(ChildUserReport $userReportRelatedByIdUserReported)
    {
        if ($this->getUserReportsRelatedByIdUserReported()->contains($userReportRelatedByIdUserReported)) {
            $pos = $this->collUserReportsRelatedByIdUserReported->search($userReportRelatedByIdUserReported);
            $this->collUserReportsRelatedByIdUserReported->remove($pos);
            if (null === $this->userReportsRelatedByIdUserReportedScheduledForDeletion) {
                $this->userReportsRelatedByIdUserReportedScheduledForDeletion = clone $this->collUserReportsRelatedByIdUserReported;
                $this->userReportsRelatedByIdUserReportedScheduledForDeletion->clear();
            }
            $this->userReportsRelatedByIdUserReportedScheduledForDeletion[]= clone $userReportRelatedByIdUserReported;
            $userReportRelatedByIdUserReported->setUserReported(null);
        }

        return $this;
    }

    /**
     * Clears out the collBugReports collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addBugReports()
     */
    public function clearBugReports()
    {
        $this->collBugReports = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collBugReports collection loaded partially.
     */
    public function resetPartialBugReports($v = true)
    {
        $this->collBugReportsPartial = $v;
    }

    /**
     * Initializes the collBugReports collection.
     *
     * By default this just sets the collBugReports collection to an empty array (like clearcollBugReports());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initBugReports($overrideExisting = true)
    {
        if (null !== $this->collBugReports && !$overrideExisting) {
            return;
        }
        $this->collBugReports = new ObjectCollection();
        $this->collBugReports->setModel('\Models\BugReport');
    }

    /**
     * Gets an array of ChildBugReport objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildBugReport[] List of ChildBugReport objects
     * @throws PropelException
     */
    public function getBugReports(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collBugReportsPartial && !$this->isNew();
        if (null === $this->collBugReports || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collBugReports) {
                // return empty collection
                $this->initBugReports();
            } else {
                $collBugReports = ChildBugReportQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collBugReportsPartial && count($collBugReports)) {
                        $this->initBugReports(false);

                        foreach ($collBugReports as $obj) {
                            if (false == $this->collBugReports->contains($obj)) {
                                $this->collBugReports->append($obj);
                            }
                        }

                        $this->collBugReportsPartial = true;
                    }

                    return $collBugReports;
                }

                if ($partial && $this->collBugReports) {
                    foreach ($this->collBugReports as $obj) {
                        if ($obj->isNew()) {
                            $collBugReports[] = $obj;
                        }
                    }
                }

                $this->collBugReports = $collBugReports;
                $this->collBugReportsPartial = false;
            }
        }

        return $this->collBugReports;
    }

    /**
     * Sets a collection of ChildBugReport objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $bugReports A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function setBugReports(Collection $bugReports, ConnectionInterface $con = null)
    {
        /** @var ChildBugReport[] $bugReportsToDelete */
        $bugReportsToDelete = $this->getBugReports(new Criteria(), $con)->diff($bugReports);


        $this->bugReportsScheduledForDeletion = $bugReportsToDelete;

        foreach ($bugReportsToDelete as $bugReportRemoved) {
            $bugReportRemoved->setUser(null);
        }

        $this->collBugReports = null;
        foreach ($bugReports as $bugReport) {
            $this->addBugReport($bugReport);
        }

        $this->collBugReports = $bugReports;
        $this->collBugReportsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related BugReport objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related BugReport objects.
     * @throws PropelException
     */
    public function countBugReports(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collBugReportsPartial && !$this->isNew();
        if (null === $this->collBugReports || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collBugReports) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getBugReports());
            }

            $query = ChildBugReportQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collBugReports);
    }

    /**
     * Method called to associate a ChildBugReport object to this object
     * through the ChildBugReport foreign key attribute.
     *
     * @param  ChildBugReport $l ChildBugReport
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function addBugReport(ChildBugReport $l)
    {
        if ($this->collBugReports === null) {
            $this->initBugReports();
            $this->collBugReportsPartial = true;
        }

        if (!$this->collBugReports->contains($l)) {
            $this->doAddBugReport($l);

            if ($this->bugReportsScheduledForDeletion and $this->bugReportsScheduledForDeletion->contains($l)) {
                $this->bugReportsScheduledForDeletion->remove($this->bugReportsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildBugReport $bugReport The ChildBugReport object to add.
     */
    protected function doAddBugReport(ChildBugReport $bugReport)
    {
        $this->collBugReports[]= $bugReport;
        $bugReport->setUser($this);
    }

    /**
     * @param  ChildBugReport $bugReport The ChildBugReport object to remove.
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function removeBugReport(ChildBugReport $bugReport)
    {
        if ($this->getBugReports()->contains($bugReport)) {
            $pos = $this->collBugReports->search($bugReport);
            $this->collBugReports->remove($pos);
            if (null === $this->bugReportsScheduledForDeletion) {
                $this->bugReportsScheduledForDeletion = clone $this->collBugReports;
                $this->bugReportsScheduledForDeletion->clear();
            }
            $this->bugReportsScheduledForDeletion[]= clone $bugReport;
            $bugReport->setUser(null);
        }

        return $this;
    }

    /**
     * Clears out the collIdeasRelatedByIdUser collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addIdeasRelatedByIdUser()
     */
    public function clearIdeasRelatedByIdUser()
    {
        $this->collIdeasRelatedByIdUser = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collIdeasRelatedByIdUser collection loaded partially.
     */
    public function resetPartialIdeasRelatedByIdUser($v = true)
    {
        $this->collIdeasRelatedByIdUserPartial = $v;
    }

    /**
     * Initializes the collIdeasRelatedByIdUser collection.
     *
     * By default this just sets the collIdeasRelatedByIdUser collection to an empty array (like clearcollIdeasRelatedByIdUser());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initIdeasRelatedByIdUser($overrideExisting = true)
    {
        if (null !== $this->collIdeasRelatedByIdUser && !$overrideExisting) {
            return;
        }
        $this->collIdeasRelatedByIdUser = new ObjectCollection();
        $this->collIdeasRelatedByIdUser->setModel('\Models\Idea');
    }

    /**
     * Gets an array of ChildIdea objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildIdea[] List of ChildIdea objects
     * @throws PropelException
     */
    public function getIdeasRelatedByIdUser(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collIdeasRelatedByIdUserPartial && !$this->isNew();
        if (null === $this->collIdeasRelatedByIdUser || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collIdeasRelatedByIdUser) {
                // return empty collection
                $this->initIdeasRelatedByIdUser();
            } else {
                $collIdeasRelatedByIdUser = ChildIdeaQuery::create(null, $criteria)
                    ->filterByUserAuthor($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collIdeasRelatedByIdUserPartial && count($collIdeasRelatedByIdUser)) {
                        $this->initIdeasRelatedByIdUser(false);

                        foreach ($collIdeasRelatedByIdUser as $obj) {
                            if (false == $this->collIdeasRelatedByIdUser->contains($obj)) {
                                $this->collIdeasRelatedByIdUser->append($obj);
                            }
                        }

                        $this->collIdeasRelatedByIdUserPartial = true;
                    }

                    return $collIdeasRelatedByIdUser;
                }

                if ($partial && $this->collIdeasRelatedByIdUser) {
                    foreach ($this->collIdeasRelatedByIdUser as $obj) {
                        if ($obj->isNew()) {
                            $collIdeasRelatedByIdUser[] = $obj;
                        }
                    }
                }

                $this->collIdeasRelatedByIdUser = $collIdeasRelatedByIdUser;
                $this->collIdeasRelatedByIdUserPartial = false;
            }
        }

        return $this->collIdeasRelatedByIdUser;
    }

    /**
     * Sets a collection of ChildIdea objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $ideasRelatedByIdUser A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function setIdeasRelatedByIdUser(Collection $ideasRelatedByIdUser, ConnectionInterface $con = null)
    {
        /** @var ChildIdea[] $ideasRelatedByIdUserToDelete */
        $ideasRelatedByIdUserToDelete = $this->getIdeasRelatedByIdUser(new Criteria(), $con)->diff($ideasRelatedByIdUser);


        $this->ideasRelatedByIdUserScheduledForDeletion = $ideasRelatedByIdUserToDelete;

        foreach ($ideasRelatedByIdUserToDelete as $ideaRelatedByIdUserRemoved) {
            $ideaRelatedByIdUserRemoved->setUserAuthor(null);
        }

        $this->collIdeasRelatedByIdUser = null;
        foreach ($ideasRelatedByIdUser as $ideaRelatedByIdUser) {
            $this->addIdeaRelatedByIdUser($ideaRelatedByIdUser);
        }

        $this->collIdeasRelatedByIdUser = $ideasRelatedByIdUser;
        $this->collIdeasRelatedByIdUserPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Idea objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Idea objects.
     * @throws PropelException
     */
    public function countIdeasRelatedByIdUser(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collIdeasRelatedByIdUserPartial && !$this->isNew();
        if (null === $this->collIdeasRelatedByIdUser || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collIdeasRelatedByIdUser) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getIdeasRelatedByIdUser());
            }

            $query = ChildIdeaQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUserAuthor($this)
                ->count($con);
        }

        return count($this->collIdeasRelatedByIdUser);
    }

    /**
     * Method called to associate a ChildIdea object to this object
     * through the ChildIdea foreign key attribute.
     *
     * @param  ChildIdea $l ChildIdea
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function addIdeaRelatedByIdUser(ChildIdea $l)
    {
        if ($this->collIdeasRelatedByIdUser === null) {
            $this->initIdeasRelatedByIdUser();
            $this->collIdeasRelatedByIdUserPartial = true;
        }

        if (!$this->collIdeasRelatedByIdUser->contains($l)) {
            $this->doAddIdeaRelatedByIdUser($l);

            if ($this->ideasRelatedByIdUserScheduledForDeletion and $this->ideasRelatedByIdUserScheduledForDeletion->contains($l)) {
                $this->ideasRelatedByIdUserScheduledForDeletion->remove($this->ideasRelatedByIdUserScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildIdea $ideaRelatedByIdUser The ChildIdea object to add.
     */
    protected function doAddIdeaRelatedByIdUser(ChildIdea $ideaRelatedByIdUser)
    {
        $this->collIdeasRelatedByIdUser[]= $ideaRelatedByIdUser;
        $ideaRelatedByIdUser->setUserAuthor($this);
    }

    /**
     * @param  ChildIdea $ideaRelatedByIdUser The ChildIdea object to remove.
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function removeIdeaRelatedByIdUser(ChildIdea $ideaRelatedByIdUser)
    {
        if ($this->getIdeasRelatedByIdUser()->contains($ideaRelatedByIdUser)) {
            $pos = $this->collIdeasRelatedByIdUser->search($ideaRelatedByIdUser);
            $this->collIdeasRelatedByIdUser->remove($pos);
            if (null === $this->ideasRelatedByIdUserScheduledForDeletion) {
                $this->ideasRelatedByIdUserScheduledForDeletion = clone $this->collIdeasRelatedByIdUser;
                $this->ideasRelatedByIdUserScheduledForDeletion->clear();
            }
            $this->ideasRelatedByIdUserScheduledForDeletion[]= clone $ideaRelatedByIdUser;
            $ideaRelatedByIdUser->setUserAuthor(null);
        }

        return $this;
    }

    /**
     * Clears out the collIdeasRelatedByApprovedBy collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addIdeasRelatedByApprovedBy()
     */
    public function clearIdeasRelatedByApprovedBy()
    {
        $this->collIdeasRelatedByApprovedBy = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collIdeasRelatedByApprovedBy collection loaded partially.
     */
    public function resetPartialIdeasRelatedByApprovedBy($v = true)
    {
        $this->collIdeasRelatedByApprovedByPartial = $v;
    }

    /**
     * Initializes the collIdeasRelatedByApprovedBy collection.
     *
     * By default this just sets the collIdeasRelatedByApprovedBy collection to an empty array (like clearcollIdeasRelatedByApprovedBy());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initIdeasRelatedByApprovedBy($overrideExisting = true)
    {
        if (null !== $this->collIdeasRelatedByApprovedBy && !$overrideExisting) {
            return;
        }
        $this->collIdeasRelatedByApprovedBy = new ObjectCollection();
        $this->collIdeasRelatedByApprovedBy->setModel('\Models\Idea');
    }

    /**
     * Gets an array of ChildIdea objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildIdea[] List of ChildIdea objects
     * @throws PropelException
     */
    public function getIdeasRelatedByApprovedBy(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collIdeasRelatedByApprovedByPartial && !$this->isNew();
        if (null === $this->collIdeasRelatedByApprovedBy || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collIdeasRelatedByApprovedBy) {
                // return empty collection
                $this->initIdeasRelatedByApprovedBy();
            } else {
                $collIdeasRelatedByApprovedBy = ChildIdeaQuery::create(null, $criteria)
                    ->filterByUserApproved($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collIdeasRelatedByApprovedByPartial && count($collIdeasRelatedByApprovedBy)) {
                        $this->initIdeasRelatedByApprovedBy(false);

                        foreach ($collIdeasRelatedByApprovedBy as $obj) {
                            if (false == $this->collIdeasRelatedByApprovedBy->contains($obj)) {
                                $this->collIdeasRelatedByApprovedBy->append($obj);
                            }
                        }

                        $this->collIdeasRelatedByApprovedByPartial = true;
                    }

                    return $collIdeasRelatedByApprovedBy;
                }

                if ($partial && $this->collIdeasRelatedByApprovedBy) {
                    foreach ($this->collIdeasRelatedByApprovedBy as $obj) {
                        if ($obj->isNew()) {
                            $collIdeasRelatedByApprovedBy[] = $obj;
                        }
                    }
                }

                $this->collIdeasRelatedByApprovedBy = $collIdeasRelatedByApprovedBy;
                $this->collIdeasRelatedByApprovedByPartial = false;
            }
        }

        return $this->collIdeasRelatedByApprovedBy;
    }

    /**
     * Sets a collection of ChildIdea objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $ideasRelatedByApprovedBy A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function setIdeasRelatedByApprovedBy(Collection $ideasRelatedByApprovedBy, ConnectionInterface $con = null)
    {
        /** @var ChildIdea[] $ideasRelatedByApprovedByToDelete */
        $ideasRelatedByApprovedByToDelete = $this->getIdeasRelatedByApprovedBy(new Criteria(), $con)->diff($ideasRelatedByApprovedBy);


        $this->ideasRelatedByApprovedByScheduledForDeletion = $ideasRelatedByApprovedByToDelete;

        foreach ($ideasRelatedByApprovedByToDelete as $ideaRelatedByApprovedByRemoved) {
            $ideaRelatedByApprovedByRemoved->setUserApproved(null);
        }

        $this->collIdeasRelatedByApprovedBy = null;
        foreach ($ideasRelatedByApprovedBy as $ideaRelatedByApprovedBy) {
            $this->addIdeaRelatedByApprovedBy($ideaRelatedByApprovedBy);
        }

        $this->collIdeasRelatedByApprovedBy = $ideasRelatedByApprovedBy;
        $this->collIdeasRelatedByApprovedByPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Idea objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Idea objects.
     * @throws PropelException
     */
    public function countIdeasRelatedByApprovedBy(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collIdeasRelatedByApprovedByPartial && !$this->isNew();
        if (null === $this->collIdeasRelatedByApprovedBy || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collIdeasRelatedByApprovedBy) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getIdeasRelatedByApprovedBy());
            }

            $query = ChildIdeaQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUserApproved($this)
                ->count($con);
        }

        return count($this->collIdeasRelatedByApprovedBy);
    }

    /**
     * Method called to associate a ChildIdea object to this object
     * through the ChildIdea foreign key attribute.
     *
     * @param  ChildIdea $l ChildIdea
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function addIdeaRelatedByApprovedBy(ChildIdea $l)
    {
        if ($this->collIdeasRelatedByApprovedBy === null) {
            $this->initIdeasRelatedByApprovedBy();
            $this->collIdeasRelatedByApprovedByPartial = true;
        }

        if (!$this->collIdeasRelatedByApprovedBy->contains($l)) {
            $this->doAddIdeaRelatedByApprovedBy($l);

            if ($this->ideasRelatedByApprovedByScheduledForDeletion and $this->ideasRelatedByApprovedByScheduledForDeletion->contains($l)) {
                $this->ideasRelatedByApprovedByScheduledForDeletion->remove($this->ideasRelatedByApprovedByScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildIdea $ideaRelatedByApprovedBy The ChildIdea object to add.
     */
    protected function doAddIdeaRelatedByApprovedBy(ChildIdea $ideaRelatedByApprovedBy)
    {
        $this->collIdeasRelatedByApprovedBy[]= $ideaRelatedByApprovedBy;
        $ideaRelatedByApprovedBy->setUserApproved($this);
    }

    /**
     * @param  ChildIdea $ideaRelatedByApprovedBy The ChildIdea object to remove.
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function removeIdeaRelatedByApprovedBy(ChildIdea $ideaRelatedByApprovedBy)
    {
        if ($this->getIdeasRelatedByApprovedBy()->contains($ideaRelatedByApprovedBy)) {
            $pos = $this->collIdeasRelatedByApprovedBy->search($ideaRelatedByApprovedBy);
            $this->collIdeasRelatedByApprovedBy->remove($pos);
            if (null === $this->ideasRelatedByApprovedByScheduledForDeletion) {
                $this->ideasRelatedByApprovedByScheduledForDeletion = clone $this->collIdeasRelatedByApprovedBy;
                $this->ideasRelatedByApprovedByScheduledForDeletion->clear();
            }
            $this->ideasRelatedByApprovedByScheduledForDeletion[]= $ideaRelatedByApprovedBy;
            $ideaRelatedByApprovedBy->setUserApproved(null);
        }

        return $this;
    }

    /**
     * Clears out the collMembershipApplications collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addMembershipApplications()
     */
    public function clearMembershipApplications()
    {
        $this->collMembershipApplications = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collMembershipApplications collection loaded partially.
     */
    public function resetPartialMembershipApplications($v = true)
    {
        $this->collMembershipApplicationsPartial = $v;
    }

    /**
     * Initializes the collMembershipApplications collection.
     *
     * By default this just sets the collMembershipApplications collection to an empty array (like clearcollMembershipApplications());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initMembershipApplications($overrideExisting = true)
    {
        if (null !== $this->collMembershipApplications && !$overrideExisting) {
            return;
        }
        $this->collMembershipApplications = new ObjectCollection();
        $this->collMembershipApplications->setModel('\Models\MembershipApplication');
    }

    /**
     * Gets an array of ChildMembershipApplication objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildMembershipApplication[] List of ChildMembershipApplication objects
     * @throws PropelException
     */
    public function getMembershipApplications(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collMembershipApplicationsPartial && !$this->isNew();
        if (null === $this->collMembershipApplications || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collMembershipApplications) {
                // return empty collection
                $this->initMembershipApplications();
            } else {
                $collMembershipApplications = ChildMembershipApplicationQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collMembershipApplicationsPartial && count($collMembershipApplications)) {
                        $this->initMembershipApplications(false);

                        foreach ($collMembershipApplications as $obj) {
                            if (false == $this->collMembershipApplications->contains($obj)) {
                                $this->collMembershipApplications->append($obj);
                            }
                        }

                        $this->collMembershipApplicationsPartial = true;
                    }

                    return $collMembershipApplications;
                }

                if ($partial && $this->collMembershipApplications) {
                    foreach ($this->collMembershipApplications as $obj) {
                        if ($obj->isNew()) {
                            $collMembershipApplications[] = $obj;
                        }
                    }
                }

                $this->collMembershipApplications = $collMembershipApplications;
                $this->collMembershipApplicationsPartial = false;
            }
        }

        return $this->collMembershipApplications;
    }

    /**
     * Sets a collection of ChildMembershipApplication objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $membershipApplications A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function setMembershipApplications(Collection $membershipApplications, ConnectionInterface $con = null)
    {
        /** @var ChildMembershipApplication[] $membershipApplicationsToDelete */
        $membershipApplicationsToDelete = $this->getMembershipApplications(new Criteria(), $con)->diff($membershipApplications);


        $this->membershipApplicationsScheduledForDeletion = $membershipApplicationsToDelete;

        foreach ($membershipApplicationsToDelete as $membershipApplicationRemoved) {
            $membershipApplicationRemoved->setUser(null);
        }

        $this->collMembershipApplications = null;
        foreach ($membershipApplications as $membershipApplication) {
            $this->addMembershipApplication($membershipApplication);
        }

        $this->collMembershipApplications = $membershipApplications;
        $this->collMembershipApplicationsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related MembershipApplication objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related MembershipApplication objects.
     * @throws PropelException
     */
    public function countMembershipApplications(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collMembershipApplicationsPartial && !$this->isNew();
        if (null === $this->collMembershipApplications || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collMembershipApplications) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getMembershipApplications());
            }

            $query = ChildMembershipApplicationQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collMembershipApplications);
    }

    /**
     * Method called to associate a ChildMembershipApplication object to this object
     * through the ChildMembershipApplication foreign key attribute.
     *
     * @param  ChildMembershipApplication $l ChildMembershipApplication
     * @return $this|\Models\User The current object (for fluent API support)
     */
    public function addMembershipApplication(ChildMembershipApplication $l)
    {
        if ($this->collMembershipApplications === null) {
            $this->initMembershipApplications();
            $this->collMembershipApplicationsPartial = true;
        }

        if (!$this->collMembershipApplications->contains($l)) {
            $this->doAddMembershipApplication($l);

            if ($this->membershipApplicationsScheduledForDeletion and $this->membershipApplicationsScheduledForDeletion->contains($l)) {
                $this->membershipApplicationsScheduledForDeletion->remove($this->membershipApplicationsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildMembershipApplication $membershipApplication The ChildMembershipApplication object to add.
     */
    protected function doAddMembershipApplication(ChildMembershipApplication $membershipApplication)
    {
        $this->collMembershipApplications[]= $membershipApplication;
        $membershipApplication->setUser($this);
    }

    /**
     * @param  ChildMembershipApplication $membershipApplication The ChildMembershipApplication object to remove.
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function removeMembershipApplication(ChildMembershipApplication $membershipApplication)
    {
        if ($this->getMembershipApplications()->contains($membershipApplication)) {
            $pos = $this->collMembershipApplications->search($membershipApplication);
            $this->collMembershipApplications->remove($pos);
            if (null === $this->membershipApplicationsScheduledForDeletion) {
                $this->membershipApplicationsScheduledForDeletion = clone $this->collMembershipApplications;
                $this->membershipApplicationsScheduledForDeletion->clear();
            }
            $this->membershipApplicationsScheduledForDeletion[]= clone $membershipApplication;
            $membershipApplication->setUser(null);
        }

        return $this;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aImage) {
            $this->aImage->removeUser($this);
        }
        if (null !== $this->aMember) {
            $this->aMember->removeUser($this);
        }
        $this->id = null;
        $this->id_member = null;
        $this->username = null;
        $this->member_from = null;
        $this->url = null;
        $this->email = null;
        $this->email_confirmed_at = null;
        $this->email_confirm_token = null;
        $this->password = null;
        $this->password_reset_token = null;
        $this->permissions = null;
        $this->signin_count = null;
        $this->id_image = null;
        $this->last_signin_at = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collArticles) {
                foreach ($this->collArticles as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collComments) {
                foreach ($this->collComments as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRatings) {
                foreach ($this->collRatings as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUserReportsRelatedByIdUser) {
                foreach ($this->collUserReportsRelatedByIdUser as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUserReportsRelatedByIdUserReported) {
                foreach ($this->collUserReportsRelatedByIdUserReported as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collBugReports) {
                foreach ($this->collBugReports as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collIdeasRelatedByIdUser) {
                foreach ($this->collIdeasRelatedByIdUser as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collIdeasRelatedByApprovedBy) {
                foreach ($this->collIdeasRelatedByApprovedBy as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collMembershipApplications) {
                foreach ($this->collMembershipApplications as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collArticles = null;
        $this->collComments = null;
        $this->collRatings = null;
        $this->collUserReportsRelatedByIdUser = null;
        $this->collUserReportsRelatedByIdUserReported = null;
        $this->collBugReports = null;
        $this->collIdeasRelatedByIdUser = null;
        $this->collIdeasRelatedByApprovedBy = null;
        $this->collMembershipApplications = null;
        $this->aImage = null;
        $this->aMember = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(UserTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     $this|ChildUser The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[UserTableMap::COL_UPDATED_AT] = true;

        return $this;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
