<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config = array(
  /* Login validation rules */
  'auth/login_post' => array(
    array(
      'field' => 'email',
      'label' => 'lang:email',
      'rules' => 'trim|required|valid_email|db_exists[users.email]',
      'errors' => array(
        'required' => 'lang:required',
        'valid_email' => 'lang:valid_email',
        'db_exists' => 'lang:db_exists'
      )
    ),
    array(
      'field' => 'password',
      'label' => 'lang:password',
      'rules' => 'trim|required|min_length[8]|alpha_and_numeric',
      'errors' => array(
        'required' => 'lang:required',
        'min_length' => 'lang:min_length',
        'alpha_and_numeric' => 'lang:alpha_and_numeric'
      )
    )
  ),
  /* Signup to the platform */
  'auth/verify_post' => array(
    array(
      'field' => 'token',
      'label' => 'lang:token',
      'rules' => 'trim',
      'errors' => array(
      )
    )
  ),
  /* Signup to the platform */
  'auth/signup_post' => array(
    array(
      'field' => 'email',
      'label' => 'lang:email',
      'rules' => 'trim|required|valid_email|db_not_exists[users.email]',
      'errors' => array(
        'required' => 'lang:required',
        'valid_email' => 'lang:valid_email',
        'db_not_exists' => 'lang:db_not_exists'
      )
    ),
    array(
      'field' => 'password',
      'label' => 'lang:password',
      'rules' => 'trim|required|min_length[8]|alpha_and_numeric',
      'errors' => array(
        'required' => 'lang:required',
        'min_length' => 'lang:min_length',
        'alpha_and_numeric' => 'lang:alpha_and_numeric'
      )
    ),
    array(
      'field' => 'firstname',
      'label' => 'lang:firstname',
      'rules' => 'trim|required',
      'errors' => array(
        'required' => 'lang:required'
      )
    ),
    array(
      'field' => 'lastname',
      'label' => 'lang:lastname',
      'rules' => 'trim|required',
      'errors' => array(
        'required' => 'lang:required'
      )
    )
  ),
  /* Reset password validation rules */
  'data/posts_post' => array(
    array(
      'field' => 'content',
      'label' => 'lang:content',
      'rules' => 'trim|required',
      'errors' => array(
        'required' => 'lang:required'
      )
    )
  ),
  /* Recover forgotten password validation rules */
  'auth/recover_post' => array(
    array(
      'field' => 'email',
      'label' => 'lang:email',
      'rules' => 'trim|required|valid_email|db_exists[users.email]',
      'errors' => array(
        'required' => 'lang:required',
        'valid_email' => 'lang:valid_email',
        'db_exists' => 'lang:db_exists'
      )
    )
  ),
  /* Send friend request validation rules */
  'data/requests_post' => array(
    array(
      'field' => 'id',
      'label' => 'lang:id',
      'rules' => 'trim|required|numeric',
      'errors' => array(
        'required' => 'lang:required',
        'numeric' => 'lang:numeric'
      )
    )
  ),
  /* Accept friend request validation rules */
  'data/requests_put' => array(
    array(
      'field' => 'id',
      'label' => 'lang:id',
      'rules' => 'trim|required|numeric',
      'errors' => array(
        'required' => 'lang:required',
        'numeric' => 'lang:numeric'
      )
    )
  )
);