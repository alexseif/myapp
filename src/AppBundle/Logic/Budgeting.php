<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Logic;

use \AppBundle\Service\CurrencyService;

/**
 * Description of Budgeting
 *
 * @author Alex Seif <me@alexseif.com>
 */
class Budgeting
{

  protected $balance = 0;
  protected $currencyService;
  protected $currency;

  /**
   * 
   * @param CurrencyService $currencyService
   */
  function __construct(CurrencyService $currencyService)
  {
    $this->currencyService = $currencyService;
    $this->currency = $currencyService->get();
  }

  public function process($transactions)
  {
    foreach ($transactions as $transaction) {
      $this->balance -= $transaction->getValue() / $this->currency[$transaction->getCurrency()->getCode()];
      $transaction->balance = $this->balance;
    }
    return $transactions;
  }

}
