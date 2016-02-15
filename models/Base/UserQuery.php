<?php

namespace Models\Base;

use \Exception;
use \PDO;
use Models\User as ChildUser;
use Models\UserQuery as ChildUserQuery;
use Models\Map\UserTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'users' table.
 *
 *
 *
 * @method     ChildUserQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildUserQuery orderByIdMember($order = Criteria::ASC) Order by the id_member column
 * @method     ChildUserQuery orderByUsername($order = Criteria::ASC) Order by the username column
 * @method     ChildUserQuery orderByMemberFrom($order = Criteria::ASC) Order by the member_from column
 * @method     ChildUserQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method     ChildUserQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     ChildUserQuery orderByEmailConfirmedAt($order = Criteria::ASC) Order by the email_confirmed_at column
 * @method     ChildUserQuery orderByEmailConfirmToken($order = Criteria::ASC) Order by the email_confirm_token column
 * @method     ChildUserQuery orderByPassword($order = Criteria::ASC) Order by the password column
 * @method     ChildUserQuery orderByPasswordResetToken($order = Criteria::ASC) Order by the password_reset_token column
 * @method     ChildUserQuery orderByPermissions($order = Criteria::ASC) Order by the permissions column
 * @method     ChildUserQuery orderBySigninCount($order = Criteria::ASC) Order by the signin_count column
 * @method     ChildUserQuery orderByIdImage($order = Criteria::ASC) Order by the id_image column
 * @method     ChildUserQuery orderByLastSigninAt($order = Criteria::ASC) Order by the last_signin_at column
 * @method     ChildUserQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildUserQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildUserQuery groupById() Group by the id column
 * @method     ChildUserQuery groupByIdMember() Group by the id_member column
 * @method     ChildUserQuery groupByUsername() Group by the username column
 * @method     ChildUserQuery groupByMemberFrom() Group by the member_from column
 * @method     ChildUserQuery groupByUrl() Group by the url column
 * @method     ChildUserQuery groupByEmail() Group by the email column
 * @method     ChildUserQuery groupByEmailConfirmedAt() Group by the email_confirmed_at column
 * @method     ChildUserQuery groupByEmailConfirmToken() Group by the email_confirm_token column
 * @method     ChildUserQuery groupByPassword() Group by the password column
 * @method     ChildUserQuery groupByPasswordResetToken() Group by the password_reset_token column
 * @method     ChildUserQuery groupByPermissions() Group by the permissions column
 * @method     ChildUserQuery groupBySigninCount() Group by the signin_count column
 * @method     ChildUserQuery groupByIdImage() Group by the id_image column
 * @method     ChildUserQuery groupByLastSigninAt() Group by the last_signin_at column
 * @method     ChildUserQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildUserQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildUserQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildUserQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildUserQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildUserQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildUserQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildUserQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildUserQuery leftJoinImage($relationAlias = null) Adds a LEFT JOIN clause to the query using the Image relation
 * @method     ChildUserQuery rightJoinImage($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Image relation
 * @method     ChildUserQuery innerJoinImage($relationAlias = null) Adds a INNER JOIN clause to the query using the Image relation
 *
 * @method     ChildUserQuery joinWithImage($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Image relation
 *
 * @method     ChildUserQuery leftJoinWithImage() Adds a LEFT JOIN clause and with to the query using the Image relation
 * @method     ChildUserQuery rightJoinWithImage() Adds a RIGHT JOIN clause and with to the query using the Image relation
 * @method     ChildUserQuery innerJoinWithImage() Adds a INNER JOIN clause and with to the query using the Image relation
 *
 * @method     ChildUserQuery leftJoinMember($relationAlias = null) Adds a LEFT JOIN clause to the query using the Member relation
 * @method     ChildUserQuery rightJoinMember($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Member relation
 * @method     ChildUserQuery innerJoinMember($relationAlias = null) Adds a INNER JOIN clause to the query using the Member relation
 *
 * @method     ChildUserQuery joinWithMember($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Member relation
 *
 * @method     ChildUserQuery leftJoinWithMember() Adds a LEFT JOIN clause and with to the query using the Member relation
 * @method     ChildUserQuery rightJoinWithMember() Adds a RIGHT JOIN clause and with to the query using the Member relation
 * @method     ChildUserQuery innerJoinWithMember() Adds a INNER JOIN clause and with to the query using the Member relation
 *
 * @method     ChildUserQuery leftJoinArticle($relationAlias = null) Adds a LEFT JOIN clause to the query using the Article relation
 * @method     ChildUserQuery rightJoinArticle($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Article relation
 * @method     ChildUserQuery innerJoinArticle($relationAlias = null) Adds a INNER JOIN clause to the query using the Article relation
 *
 * @method     ChildUserQuery joinWithArticle($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Article relation
 *
 * @method     ChildUserQuery leftJoinWithArticle() Adds a LEFT JOIN clause and with to the query using the Article relation
 * @method     ChildUserQuery rightJoinWithArticle() Adds a RIGHT JOIN clause and with to the query using the Article relation
 * @method     ChildUserQuery innerJoinWithArticle() Adds a INNER JOIN clause and with to the query using the Article relation
 *
 * @method     ChildUserQuery leftJoinComment($relationAlias = null) Adds a LEFT JOIN clause to the query using the Comment relation
 * @method     ChildUserQuery rightJoinComment($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Comment relation
 * @method     ChildUserQuery innerJoinComment($relationAlias = null) Adds a INNER JOIN clause to the query using the Comment relation
 *
 * @method     ChildUserQuery joinWithComment($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Comment relation
 *
 * @method     ChildUserQuery leftJoinWithComment() Adds a LEFT JOIN clause and with to the query using the Comment relation
 * @method     ChildUserQuery rightJoinWithComment() Adds a RIGHT JOIN clause and with to the query using the Comment relation
 * @method     ChildUserQuery innerJoinWithComment() Adds a INNER JOIN clause and with to the query using the Comment relation
 *
 * @method     ChildUserQuery leftJoinRating($relationAlias = null) Adds a LEFT JOIN clause to the query using the Rating relation
 * @method     ChildUserQuery rightJoinRating($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Rating relation
 * @method     ChildUserQuery innerJoinRating($relationAlias = null) Adds a INNER JOIN clause to the query using the Rating relation
 *
 * @method     ChildUserQuery joinWithRating($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Rating relation
 *
 * @method     ChildUserQuery leftJoinWithRating() Adds a LEFT JOIN clause and with to the query using the Rating relation
 * @method     ChildUserQuery rightJoinWithRating() Adds a RIGHT JOIN clause and with to the query using the Rating relation
 * @method     ChildUserQuery innerJoinWithRating() Adds a INNER JOIN clause and with to the query using the Rating relation
 *
 * @method     ChildUserQuery leftJoinUserReportRelatedByIdUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserReportRelatedByIdUser relation
 * @method     ChildUserQuery rightJoinUserReportRelatedByIdUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserReportRelatedByIdUser relation
 * @method     ChildUserQuery innerJoinUserReportRelatedByIdUser($relationAlias = null) Adds a INNER JOIN clause to the query using the UserReportRelatedByIdUser relation
 *
 * @method     ChildUserQuery joinWithUserReportRelatedByIdUser($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the UserReportRelatedByIdUser relation
 *
 * @method     ChildUserQuery leftJoinWithUserReportRelatedByIdUser() Adds a LEFT JOIN clause and with to the query using the UserReportRelatedByIdUser relation
 * @method     ChildUserQuery rightJoinWithUserReportRelatedByIdUser() Adds a RIGHT JOIN clause and with to the query using the UserReportRelatedByIdUser relation
 * @method     ChildUserQuery innerJoinWithUserReportRelatedByIdUser() Adds a INNER JOIN clause and with to the query using the UserReportRelatedByIdUser relation
 *
 * @method     ChildUserQuery leftJoinUserReportRelatedByIdUserReported($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserReportRelatedByIdUserReported relation
 * @method     ChildUserQuery rightJoinUserReportRelatedByIdUserReported($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserReportRelatedByIdUserReported relation
 * @method     ChildUserQuery innerJoinUserReportRelatedByIdUserReported($relationAlias = null) Adds a INNER JOIN clause to the query using the UserReportRelatedByIdUserReported relation
 *
 * @method     ChildUserQuery joinWithUserReportRelatedByIdUserReported($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the UserReportRelatedByIdUserReported relation
 *
 * @method     ChildUserQuery leftJoinWithUserReportRelatedByIdUserReported() Adds a LEFT JOIN clause and with to the query using the UserReportRelatedByIdUserReported relation
 * @method     ChildUserQuery rightJoinWithUserReportRelatedByIdUserReported() Adds a RIGHT JOIN clause and with to the query using the UserReportRelatedByIdUserReported relation
 * @method     ChildUserQuery innerJoinWithUserReportRelatedByIdUserReported() Adds a INNER JOIN clause and with to the query using the UserReportRelatedByIdUserReported relation
 *
 * @method     ChildUserQuery leftJoinBugReport($relationAlias = null) Adds a LEFT JOIN clause to the query using the BugReport relation
 * @method     ChildUserQuery rightJoinBugReport($relationAlias = null) Adds a RIGHT JOIN clause to the query using the BugReport relation
 * @method     ChildUserQuery innerJoinBugReport($relationAlias = null) Adds a INNER JOIN clause to the query using the BugReport relation
 *
 * @method     ChildUserQuery joinWithBugReport($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the BugReport relation
 *
 * @method     ChildUserQuery leftJoinWithBugReport() Adds a LEFT JOIN clause and with to the query using the BugReport relation
 * @method     ChildUserQuery rightJoinWithBugReport() Adds a RIGHT JOIN clause and with to the query using the BugReport relation
 * @method     ChildUserQuery innerJoinWithBugReport() Adds a INNER JOIN clause and with to the query using the BugReport relation
 *
 * @method     ChildUserQuery leftJoinIdeaRelatedByIdUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the IdeaRelatedByIdUser relation
 * @method     ChildUserQuery rightJoinIdeaRelatedByIdUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the IdeaRelatedByIdUser relation
 * @method     ChildUserQuery innerJoinIdeaRelatedByIdUser($relationAlias = null) Adds a INNER JOIN clause to the query using the IdeaRelatedByIdUser relation
 *
 * @method     ChildUserQuery joinWithIdeaRelatedByIdUser($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the IdeaRelatedByIdUser relation
 *
 * @method     ChildUserQuery leftJoinWithIdeaRelatedByIdUser() Adds a LEFT JOIN clause and with to the query using the IdeaRelatedByIdUser relation
 * @method     ChildUserQuery rightJoinWithIdeaRelatedByIdUser() Adds a RIGHT JOIN clause and with to the query using the IdeaRelatedByIdUser relation
 * @method     ChildUserQuery innerJoinWithIdeaRelatedByIdUser() Adds a INNER JOIN clause and with to the query using the IdeaRelatedByIdUser relation
 *
 * @method     ChildUserQuery leftJoinIdeaRelatedByApprovedBy($relationAlias = null) Adds a LEFT JOIN clause to the query using the IdeaRelatedByApprovedBy relation
 * @method     ChildUserQuery rightJoinIdeaRelatedByApprovedBy($relationAlias = null) Adds a RIGHT JOIN clause to the query using the IdeaRelatedByApprovedBy relation
 * @method     ChildUserQuery innerJoinIdeaRelatedByApprovedBy($relationAlias = null) Adds a INNER JOIN clause to the query using the IdeaRelatedByApprovedBy relation
 *
 * @method     ChildUserQuery joinWithIdeaRelatedByApprovedBy($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the IdeaRelatedByApprovedBy relation
 *
 * @method     ChildUserQuery leftJoinWithIdeaRelatedByApprovedBy() Adds a LEFT JOIN clause and with to the query using the IdeaRelatedByApprovedBy relation
 * @method     ChildUserQuery rightJoinWithIdeaRelatedByApprovedBy() Adds a RIGHT JOIN clause and with to the query using the IdeaRelatedByApprovedBy relation
 * @method     ChildUserQuery innerJoinWithIdeaRelatedByApprovedBy() Adds a INNER JOIN clause and with to the query using the IdeaRelatedByApprovedBy relation
 *
 * @method     ChildUserQuery leftJoinMembershipApplication($relationAlias = null) Adds a LEFT JOIN clause to the query using the MembershipApplication relation
 * @method     ChildUserQuery rightJoinMembershipApplication($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MembershipApplication relation
 * @method     ChildUserQuery innerJoinMembershipApplication($relationAlias = null) Adds a INNER JOIN clause to the query using the MembershipApplication relation
 *
 * @method     ChildUserQuery joinWithMembershipApplication($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the MembershipApplication relation
 *
 * @method     ChildUserQuery leftJoinWithMembershipApplication() Adds a LEFT JOIN clause and with to the query using the MembershipApplication relation
 * @method     ChildUserQuery rightJoinWithMembershipApplication() Adds a RIGHT JOIN clause and with to the query using the MembershipApplication relation
 * @method     ChildUserQuery innerJoinWithMembershipApplication() Adds a INNER JOIN clause and with to the query using the MembershipApplication relation
 *
 * @method     \Models\ImageQuery|\Models\MemberQuery|\Models\ArticleQuery|\Models\CommentQuery|\Models\RatingQuery|\Models\UserReportQuery|\Models\BugReportQuery|\Models\IdeaQuery|\Models\MembershipApplicationQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildUser findOne(ConnectionInterface $con = null) Return the first ChildUser matching the query
 * @method     ChildUser findOneOrCreate(ConnectionInterface $con = null) Return the first ChildUser matching the query, or a new ChildUser object populated from the query conditions when no match is found
 *
 * @method     ChildUser findOneById(int $id) Return the first ChildUser filtered by the id column
 * @method     ChildUser findOneByIdMember(int $id_member) Return the first ChildUser filtered by the id_member column
 * @method     ChildUser findOneByUsername(string $username) Return the first ChildUser filtered by the username column
 * @method     ChildUser findOneByMemberFrom(string $member_from) Return the first ChildUser filtered by the member_from column
 * @method     ChildUser findOneByUrl(string $url) Return the first ChildUser filtered by the url column
 * @method     ChildUser findOneByEmail(string $email) Return the first ChildUser filtered by the email column
 * @method     ChildUser findOneByEmailConfirmedAt(string $email_confirmed_at) Return the first ChildUser filtered by the email_confirmed_at column
 * @method     ChildUser findOneByEmailConfirmToken(string $email_confirm_token) Return the first ChildUser filtered by the email_confirm_token column
 * @method     ChildUser findOneByPassword(string $password) Return the first ChildUser filtered by the password column
 * @method     ChildUser findOneByPasswordResetToken(string $password_reset_token) Return the first ChildUser filtered by the password_reset_token column
 * @method     ChildUser findOneByPermissions(int $permissions) Return the first ChildUser filtered by the permissions column
 * @method     ChildUser findOneBySigninCount(int $signin_count) Return the first ChildUser filtered by the signin_count column
 * @method     ChildUser findOneByIdImage(int $id_image) Return the first ChildUser filtered by the id_image column
 * @method     ChildUser findOneByLastSigninAt(string $last_signin_at) Return the first ChildUser filtered by the last_signin_at column
 * @method     ChildUser findOneByCreatedAt(string $created_at) Return the first ChildUser filtered by the created_at column
 * @method     ChildUser findOneByUpdatedAt(string $updated_at) Return the first ChildUser filtered by the updated_at column *

 * @method     ChildUser requirePk($key, ConnectionInterface $con = null) Return the ChildUser by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOne(ConnectionInterface $con = null) Return the first ChildUser matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildUser requireOneById(int $id) Return the first ChildUser filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByIdMember(int $id_member) Return the first ChildUser filtered by the id_member column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByUsername(string $username) Return the first ChildUser filtered by the username column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByMemberFrom(string $member_from) Return the first ChildUser filtered by the member_from column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByUrl(string $url) Return the first ChildUser filtered by the url column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByEmail(string $email) Return the first ChildUser filtered by the email column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByEmailConfirmedAt(string $email_confirmed_at) Return the first ChildUser filtered by the email_confirmed_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByEmailConfirmToken(string $email_confirm_token) Return the first ChildUser filtered by the email_confirm_token column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByPassword(string $password) Return the first ChildUser filtered by the password column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByPasswordResetToken(string $password_reset_token) Return the first ChildUser filtered by the password_reset_token column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByPermissions(int $permissions) Return the first ChildUser filtered by the permissions column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneBySigninCount(int $signin_count) Return the first ChildUser filtered by the signin_count column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByIdImage(int $id_image) Return the first ChildUser filtered by the id_image column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByLastSigninAt(string $last_signin_at) Return the first ChildUser filtered by the last_signin_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByCreatedAt(string $created_at) Return the first ChildUser filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUser requireOneByUpdatedAt(string $updated_at) Return the first ChildUser filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildUser[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildUser objects based on current ModelCriteria
 * @method     ChildUser[]|ObjectCollection findById(int $id) Return ChildUser objects filtered by the id column
 * @method     ChildUser[]|ObjectCollection findByIdMember(int $id_member) Return ChildUser objects filtered by the id_member column
 * @method     ChildUser[]|ObjectCollection findByUsername(string $username) Return ChildUser objects filtered by the username column
 * @method     ChildUser[]|ObjectCollection findByMemberFrom(string $member_from) Return ChildUser objects filtered by the member_from column
 * @method     ChildUser[]|ObjectCollection findByUrl(string $url) Return ChildUser objects filtered by the url column
 * @method     ChildUser[]|ObjectCollection findByEmail(string $email) Return ChildUser objects filtered by the email column
 * @method     ChildUser[]|ObjectCollection findByEmailConfirmedAt(string $email_confirmed_at) Return ChildUser objects filtered by the email_confirmed_at column
 * @method     ChildUser[]|ObjectCollection findByEmailConfirmToken(string $email_confirm_token) Return ChildUser objects filtered by the email_confirm_token column
 * @method     ChildUser[]|ObjectCollection findByPassword(string $password) Return ChildUser objects filtered by the password column
 * @method     ChildUser[]|ObjectCollection findByPasswordResetToken(string $password_reset_token) Return ChildUser objects filtered by the password_reset_token column
 * @method     ChildUser[]|ObjectCollection findByPermissions(int $permissions) Return ChildUser objects filtered by the permissions column
 * @method     ChildUser[]|ObjectCollection findBySigninCount(int $signin_count) Return ChildUser objects filtered by the signin_count column
 * @method     ChildUser[]|ObjectCollection findByIdImage(int $id_image) Return ChildUser objects filtered by the id_image column
 * @method     ChildUser[]|ObjectCollection findByLastSigninAt(string $last_signin_at) Return ChildUser objects filtered by the last_signin_at column
 * @method     ChildUser[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildUser objects filtered by the created_at column
 * @method     ChildUser[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildUser objects filtered by the updated_at column
 * @method     ChildUser[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class UserQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Models\Base\UserQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'dofe', $modelName = '\\Models\\User', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildUserQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildUserQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildUserQuery) {
            return $criteria;
        }
        $query = new ChildUserQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildUser|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = UserTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(UserTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildUser A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, id_member, username, member_from, url, email, email_confirmed_at, email_confirm_token, password, password_reset_token, permissions, signin_count, id_image, last_signin_at, created_at, updated_at FROM users WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildUser $obj */
            $obj = new ChildUser();
            $obj->hydrate($row);
            UserTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildUser|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(UserTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(UserTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(UserTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(UserTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the id_member column
     *
     * Example usage:
     * <code>
     * $query->filterByIdMember(1234); // WHERE id_member = 1234
     * $query->filterByIdMember(array(12, 34)); // WHERE id_member IN (12, 34)
     * $query->filterByIdMember(array('min' => 12)); // WHERE id_member > 12
     * </code>
     *
     * @see       filterByMember()
     *
     * @param     mixed $idMember The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByIdMember($idMember = null, $comparison = null)
    {
        if (is_array($idMember)) {
            $useMinMax = false;
            if (isset($idMember['min'])) {
                $this->addUsingAlias(UserTableMap::COL_ID_MEMBER, $idMember['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($idMember['max'])) {
                $this->addUsingAlias(UserTableMap::COL_ID_MEMBER, $idMember['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_ID_MEMBER, $idMember, $comparison);
    }

    /**
     * Filter the query on the username column
     *
     * Example usage:
     * <code>
     * $query->filterByUsername('fooValue');   // WHERE username = 'fooValue'
     * $query->filterByUsername('%fooValue%'); // WHERE username LIKE '%fooValue%'
     * </code>
     *
     * @param     string $username The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByUsername($username = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($username)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $username)) {
                $username = str_replace('*', '%', $username);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_USERNAME, $username, $comparison);
    }

    /**
     * Filter the query on the member_from column
     *
     * Example usage:
     * <code>
     * $query->filterByMemberFrom('2011-03-14'); // WHERE member_from = '2011-03-14'
     * $query->filterByMemberFrom('now'); // WHERE member_from = '2011-03-14'
     * $query->filterByMemberFrom(array('max' => 'yesterday')); // WHERE member_from > '2011-03-13'
     * </code>
     *
     * @param     mixed $memberFrom The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByMemberFrom($memberFrom = null, $comparison = null)
    {
        if (is_array($memberFrom)) {
            $useMinMax = false;
            if (isset($memberFrom['min'])) {
                $this->addUsingAlias(UserTableMap::COL_MEMBER_FROM, $memberFrom['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($memberFrom['max'])) {
                $this->addUsingAlias(UserTableMap::COL_MEMBER_FROM, $memberFrom['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_MEMBER_FROM, $memberFrom, $comparison);
    }

    /**
     * Filter the query on the url column
     *
     * Example usage:
     * <code>
     * $query->filterByUrl('fooValue');   // WHERE url = 'fooValue'
     * $query->filterByUrl('%fooValue%'); // WHERE url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $url The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByUrl($url = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($url)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $url)) {
                $url = str_replace('*', '%', $url);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_URL, $url, $comparison);
    }

    /**
     * Filter the query on the email column
     *
     * Example usage:
     * <code>
     * $query->filterByEmail('fooValue');   // WHERE email = 'fooValue'
     * $query->filterByEmail('%fooValue%'); // WHERE email LIKE '%fooValue%'
     * </code>
     *
     * @param     string $email The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByEmail($email = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($email)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $email)) {
                $email = str_replace('*', '%', $email);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_EMAIL, $email, $comparison);
    }

    /**
     * Filter the query on the email_confirmed_at column
     *
     * Example usage:
     * <code>
     * $query->filterByEmailConfirmedAt('2011-03-14'); // WHERE email_confirmed_at = '2011-03-14'
     * $query->filterByEmailConfirmedAt('now'); // WHERE email_confirmed_at = '2011-03-14'
     * $query->filterByEmailConfirmedAt(array('max' => 'yesterday')); // WHERE email_confirmed_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $emailConfirmedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByEmailConfirmedAt($emailConfirmedAt = null, $comparison = null)
    {
        if (is_array($emailConfirmedAt)) {
            $useMinMax = false;
            if (isset($emailConfirmedAt['min'])) {
                $this->addUsingAlias(UserTableMap::COL_EMAIL_CONFIRMED_AT, $emailConfirmedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($emailConfirmedAt['max'])) {
                $this->addUsingAlias(UserTableMap::COL_EMAIL_CONFIRMED_AT, $emailConfirmedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_EMAIL_CONFIRMED_AT, $emailConfirmedAt, $comparison);
    }

    /**
     * Filter the query on the email_confirm_token column
     *
     * Example usage:
     * <code>
     * $query->filterByEmailConfirmToken('fooValue');   // WHERE email_confirm_token = 'fooValue'
     * $query->filterByEmailConfirmToken('%fooValue%'); // WHERE email_confirm_token LIKE '%fooValue%'
     * </code>
     *
     * @param     string $emailConfirmToken The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByEmailConfirmToken($emailConfirmToken = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($emailConfirmToken)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $emailConfirmToken)) {
                $emailConfirmToken = str_replace('*', '%', $emailConfirmToken);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_EMAIL_CONFIRM_TOKEN, $emailConfirmToken, $comparison);
    }

    /**
     * Filter the query on the password column
     *
     * Example usage:
     * <code>
     * $query->filterByPassword('fooValue');   // WHERE password = 'fooValue'
     * $query->filterByPassword('%fooValue%'); // WHERE password LIKE '%fooValue%'
     * </code>
     *
     * @param     string $password The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByPassword($password = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($password)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $password)) {
                $password = str_replace('*', '%', $password);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_PASSWORD, $password, $comparison);
    }

    /**
     * Filter the query on the password_reset_token column
     *
     * Example usage:
     * <code>
     * $query->filterByPasswordResetToken('fooValue');   // WHERE password_reset_token = 'fooValue'
     * $query->filterByPasswordResetToken('%fooValue%'); // WHERE password_reset_token LIKE '%fooValue%'
     * </code>
     *
     * @param     string $passwordResetToken The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByPasswordResetToken($passwordResetToken = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($passwordResetToken)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $passwordResetToken)) {
                $passwordResetToken = str_replace('*', '%', $passwordResetToken);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_PASSWORD_RESET_TOKEN, $passwordResetToken, $comparison);
    }

    /**
     * Filter the query on the permissions column
     *
     * Example usage:
     * <code>
     * $query->filterByPermissions(1234); // WHERE permissions = 1234
     * $query->filterByPermissions(array(12, 34)); // WHERE permissions IN (12, 34)
     * $query->filterByPermissions(array('min' => 12)); // WHERE permissions > 12
     * </code>
     *
     * @param     mixed $permissions The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByPermissions($permissions = null, $comparison = null)
    {
        if (is_array($permissions)) {
            $useMinMax = false;
            if (isset($permissions['min'])) {
                $this->addUsingAlias(UserTableMap::COL_PERMISSIONS, $permissions['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($permissions['max'])) {
                $this->addUsingAlias(UserTableMap::COL_PERMISSIONS, $permissions['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_PERMISSIONS, $permissions, $comparison);
    }

    /**
     * Filter the query on the signin_count column
     *
     * Example usage:
     * <code>
     * $query->filterBySigninCount(1234); // WHERE signin_count = 1234
     * $query->filterBySigninCount(array(12, 34)); // WHERE signin_count IN (12, 34)
     * $query->filterBySigninCount(array('min' => 12)); // WHERE signin_count > 12
     * </code>
     *
     * @param     mixed $signinCount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterBySigninCount($signinCount = null, $comparison = null)
    {
        if (is_array($signinCount)) {
            $useMinMax = false;
            if (isset($signinCount['min'])) {
                $this->addUsingAlias(UserTableMap::COL_SIGNIN_COUNT, $signinCount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($signinCount['max'])) {
                $this->addUsingAlias(UserTableMap::COL_SIGNIN_COUNT, $signinCount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_SIGNIN_COUNT, $signinCount, $comparison);
    }

    /**
     * Filter the query on the id_image column
     *
     * Example usage:
     * <code>
     * $query->filterByIdImage(1234); // WHERE id_image = 1234
     * $query->filterByIdImage(array(12, 34)); // WHERE id_image IN (12, 34)
     * $query->filterByIdImage(array('min' => 12)); // WHERE id_image > 12
     * </code>
     *
     * @see       filterByImage()
     *
     * @param     mixed $idImage The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByIdImage($idImage = null, $comparison = null)
    {
        if (is_array($idImage)) {
            $useMinMax = false;
            if (isset($idImage['min'])) {
                $this->addUsingAlias(UserTableMap::COL_ID_IMAGE, $idImage['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($idImage['max'])) {
                $this->addUsingAlias(UserTableMap::COL_ID_IMAGE, $idImage['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_ID_IMAGE, $idImage, $comparison);
    }

    /**
     * Filter the query on the last_signin_at column
     *
     * Example usage:
     * <code>
     * $query->filterByLastSigninAt('2011-03-14'); // WHERE last_signin_at = '2011-03-14'
     * $query->filterByLastSigninAt('now'); // WHERE last_signin_at = '2011-03-14'
     * $query->filterByLastSigninAt(array('max' => 'yesterday')); // WHERE last_signin_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $lastSigninAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByLastSigninAt($lastSigninAt = null, $comparison = null)
    {
        if (is_array($lastSigninAt)) {
            $useMinMax = false;
            if (isset($lastSigninAt['min'])) {
                $this->addUsingAlias(UserTableMap::COL_LAST_SIGNIN_AT, $lastSigninAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastSigninAt['max'])) {
                $this->addUsingAlias(UserTableMap::COL_LAST_SIGNIN_AT, $lastSigninAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_LAST_SIGNIN_AT, $lastSigninAt, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(UserTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(UserTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(UserTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(UserTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Models\Image object
     *
     * @param \Models\Image|ObjectCollection $image The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByImage($image, $comparison = null)
    {
        if ($image instanceof \Models\Image) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID_IMAGE, $image->getId(), $comparison);
        } elseif ($image instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(UserTableMap::COL_ID_IMAGE, $image->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByImage() only accepts arguments of type \Models\Image or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Image relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function joinImage($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Image');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Image');
        }

        return $this;
    }

    /**
     * Use the Image relation Image object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Models\ImageQuery A secondary query class using the current class as primary query
     */
    public function useImageQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinImage($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Image', '\Models\ImageQuery');
    }

    /**
     * Filter the query by a related \Models\Member object
     *
     * @param \Models\Member|ObjectCollection $member The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByMember($member, $comparison = null)
    {
        if ($member instanceof \Models\Member) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID_MEMBER, $member->getId(), $comparison);
        } elseif ($member instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(UserTableMap::COL_ID_MEMBER, $member->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByMember() only accepts arguments of type \Models\Member or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Member relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function joinMember($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Member');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Member');
        }

        return $this;
    }

    /**
     * Use the Member relation Member object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Models\MemberQuery A secondary query class using the current class as primary query
     */
    public function useMemberQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinMember($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Member', '\Models\MemberQuery');
    }

    /**
     * Filter the query by a related \Models\Article object
     *
     * @param \Models\Article|ObjectCollection $article the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByArticle($article, $comparison = null)
    {
        if ($article instanceof \Models\Article) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID, $article->getIdUser(), $comparison);
        } elseif ($article instanceof ObjectCollection) {
            return $this
                ->useArticleQuery()
                ->filterByPrimaryKeys($article->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByArticle() only accepts arguments of type \Models\Article or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Article relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function joinArticle($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Article');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Article');
        }

        return $this;
    }

    /**
     * Use the Article relation Article object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Models\ArticleQuery A secondary query class using the current class as primary query
     */
    public function useArticleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinArticle($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Article', '\Models\ArticleQuery');
    }

    /**
     * Filter the query by a related \Models\Comment object
     *
     * @param \Models\Comment|ObjectCollection $comment the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByComment($comment, $comparison = null)
    {
        if ($comment instanceof \Models\Comment) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID, $comment->getIdUser(), $comparison);
        } elseif ($comment instanceof ObjectCollection) {
            return $this
                ->useCommentQuery()
                ->filterByPrimaryKeys($comment->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByComment() only accepts arguments of type \Models\Comment or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Comment relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function joinComment($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Comment');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Comment');
        }

        return $this;
    }

    /**
     * Use the Comment relation Comment object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Models\CommentQuery A secondary query class using the current class as primary query
     */
    public function useCommentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinComment($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Comment', '\Models\CommentQuery');
    }

    /**
     * Filter the query by a related \Models\Rating object
     *
     * @param \Models\Rating|ObjectCollection $rating the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByRating($rating, $comparison = null)
    {
        if ($rating instanceof \Models\Rating) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID, $rating->getIdUser(), $comparison);
        } elseif ($rating instanceof ObjectCollection) {
            return $this
                ->useRatingQuery()
                ->filterByPrimaryKeys($rating->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByRating() only accepts arguments of type \Models\Rating or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Rating relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function joinRating($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Rating');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Rating');
        }

        return $this;
    }

    /**
     * Use the Rating relation Rating object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Models\RatingQuery A secondary query class using the current class as primary query
     */
    public function useRatingQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinRating($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Rating', '\Models\RatingQuery');
    }

    /**
     * Filter the query by a related \Models\UserReport object
     *
     * @param \Models\UserReport|ObjectCollection $userReport the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByUserReportRelatedByIdUser($userReport, $comparison = null)
    {
        if ($userReport instanceof \Models\UserReport) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID, $userReport->getIdUser(), $comparison);
        } elseif ($userReport instanceof ObjectCollection) {
            return $this
                ->useUserReportRelatedByIdUserQuery()
                ->filterByPrimaryKeys($userReport->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUserReportRelatedByIdUser() only accepts arguments of type \Models\UserReport or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserReportRelatedByIdUser relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function joinUserReportRelatedByIdUser($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserReportRelatedByIdUser');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'UserReportRelatedByIdUser');
        }

        return $this;
    }

    /**
     * Use the UserReportRelatedByIdUser relation UserReport object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Models\UserReportQuery A secondary query class using the current class as primary query
     */
    public function useUserReportRelatedByIdUserQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserReportRelatedByIdUser($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserReportRelatedByIdUser', '\Models\UserReportQuery');
    }

    /**
     * Filter the query by a related \Models\UserReport object
     *
     * @param \Models\UserReport|ObjectCollection $userReport the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByUserReportRelatedByIdUserReported($userReport, $comparison = null)
    {
        if ($userReport instanceof \Models\UserReport) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID, $userReport->getIdUserReported(), $comparison);
        } elseif ($userReport instanceof ObjectCollection) {
            return $this
                ->useUserReportRelatedByIdUserReportedQuery()
                ->filterByPrimaryKeys($userReport->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUserReportRelatedByIdUserReported() only accepts arguments of type \Models\UserReport or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserReportRelatedByIdUserReported relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function joinUserReportRelatedByIdUserReported($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserReportRelatedByIdUserReported');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'UserReportRelatedByIdUserReported');
        }

        return $this;
    }

    /**
     * Use the UserReportRelatedByIdUserReported relation UserReport object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Models\UserReportQuery A secondary query class using the current class as primary query
     */
    public function useUserReportRelatedByIdUserReportedQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserReportRelatedByIdUserReported($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserReportRelatedByIdUserReported', '\Models\UserReportQuery');
    }

    /**
     * Filter the query by a related \Models\BugReport object
     *
     * @param \Models\BugReport|ObjectCollection $bugReport the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByBugReport($bugReport, $comparison = null)
    {
        if ($bugReport instanceof \Models\BugReport) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID, $bugReport->getIdUser(), $comparison);
        } elseif ($bugReport instanceof ObjectCollection) {
            return $this
                ->useBugReportQuery()
                ->filterByPrimaryKeys($bugReport->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByBugReport() only accepts arguments of type \Models\BugReport or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the BugReport relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function joinBugReport($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('BugReport');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'BugReport');
        }

        return $this;
    }

    /**
     * Use the BugReport relation BugReport object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Models\BugReportQuery A secondary query class using the current class as primary query
     */
    public function useBugReportQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinBugReport($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'BugReport', '\Models\BugReportQuery');
    }

    /**
     * Filter the query by a related \Models\Idea object
     *
     * @param \Models\Idea|ObjectCollection $idea the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByIdeaRelatedByIdUser($idea, $comparison = null)
    {
        if ($idea instanceof \Models\Idea) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID, $idea->getIdUser(), $comparison);
        } elseif ($idea instanceof ObjectCollection) {
            return $this
                ->useIdeaRelatedByIdUserQuery()
                ->filterByPrimaryKeys($idea->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByIdeaRelatedByIdUser() only accepts arguments of type \Models\Idea or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the IdeaRelatedByIdUser relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function joinIdeaRelatedByIdUser($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('IdeaRelatedByIdUser');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'IdeaRelatedByIdUser');
        }

        return $this;
    }

    /**
     * Use the IdeaRelatedByIdUser relation Idea object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Models\IdeaQuery A secondary query class using the current class as primary query
     */
    public function useIdeaRelatedByIdUserQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinIdeaRelatedByIdUser($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'IdeaRelatedByIdUser', '\Models\IdeaQuery');
    }

    /**
     * Filter the query by a related \Models\Idea object
     *
     * @param \Models\Idea|ObjectCollection $idea the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByIdeaRelatedByApprovedBy($idea, $comparison = null)
    {
        if ($idea instanceof \Models\Idea) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID, $idea->getApprovedBy(), $comparison);
        } elseif ($idea instanceof ObjectCollection) {
            return $this
                ->useIdeaRelatedByApprovedByQuery()
                ->filterByPrimaryKeys($idea->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByIdeaRelatedByApprovedBy() only accepts arguments of type \Models\Idea or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the IdeaRelatedByApprovedBy relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function joinIdeaRelatedByApprovedBy($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('IdeaRelatedByApprovedBy');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'IdeaRelatedByApprovedBy');
        }

        return $this;
    }

    /**
     * Use the IdeaRelatedByApprovedBy relation Idea object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Models\IdeaQuery A secondary query class using the current class as primary query
     */
    public function useIdeaRelatedByApprovedByQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinIdeaRelatedByApprovedBy($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'IdeaRelatedByApprovedBy', '\Models\IdeaQuery');
    }

    /**
     * Filter the query by a related \Models\MembershipApplication object
     *
     * @param \Models\MembershipApplication|ObjectCollection $membershipApplication the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByMembershipApplication($membershipApplication, $comparison = null)
    {
        if ($membershipApplication instanceof \Models\MembershipApplication) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID, $membershipApplication->getIdUser(), $comparison);
        } elseif ($membershipApplication instanceof ObjectCollection) {
            return $this
                ->useMembershipApplicationQuery()
                ->filterByPrimaryKeys($membershipApplication->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByMembershipApplication() only accepts arguments of type \Models\MembershipApplication or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MembershipApplication relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function joinMembershipApplication($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MembershipApplication');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'MembershipApplication');
        }

        return $this;
    }

    /**
     * Use the MembershipApplication relation MembershipApplication object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Models\MembershipApplicationQuery A secondary query class using the current class as primary query
     */
    public function useMembershipApplicationQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinMembershipApplication($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MembershipApplication', '\Models\MembershipApplicationQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildUser $user Object to remove from the list of results
     *
     * @return $this|ChildUserQuery The current query, for fluid interface
     */
    public function prune($user = null)
    {
        if ($user) {
            $this->addUsingAlias(UserTableMap::COL_ID, $user->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the users table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            UserTableMap::clearInstancePool();
            UserTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(UserTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            UserTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            UserTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildUserQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(UserTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildUserQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(UserTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildUserQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(UserTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildUserQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(UserTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildUserQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(UserTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildUserQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(UserTableMap::COL_CREATED_AT);
    }

} // UserQuery
