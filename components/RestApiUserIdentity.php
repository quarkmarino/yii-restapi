<?php
class RestApiUserIdentity extends CBaseUserIdentity {

  protected $token;
  protected $key;
  protected $secret;

  /** @var $user ApiUsers */
  protected $user;

  public function setKeySecret($key, $secret) {
    $this->key = $key;
    $this->secret = $secret;
  }

  public function setToken($token) {
    $this->token = $token;
  }

  /**
   * Authenticates the user.
   * The information needed to authenticate the user
   * are usually provided in the constructor.
   * @return boolean whether authentication succeeds.
   */
  public function authenticate() {
    if ($this->token) {
      $this->user = ApiUsers::model()->findByAttributes(array("token"=>$this->token));
      if ($this->user != null && !$this->user->tokenExpired()) {
        $this->errorCode = self::ERROR_NONE;
        return true;
      }
      $this->errorMessage = "Invalid Token";
    } else if ($this->key && $this->secret) {
      /** @var $user ApiUsers */
      $this->user = ApiUsers::model()->findByAttributes(array("key"=>$this->key, "secret"=>$this->secret, "active"=>1));
      if ($this->user != null) {
        $this->errorCode = self::ERROR_NONE;
        return true;
      }
      $this->errorMessage = "Invalid Key or/and Secret";
    }
    $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
    return false;
  }

  /**
   * @return ApiUsers
   */
  public function getApiUser() {
    return $this->user;
  }

  public function getId() {
    if ($this->user) return $this->user->getPrimaryKey();
    else return '';
  }

  public function getName() {
    if ($this->user) return $this->user->username;
    else return '';
  }
}