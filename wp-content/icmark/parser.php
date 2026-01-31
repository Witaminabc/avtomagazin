<?php

namespace IcmUtils;

use Bitrix\Bizproc\Workflow\Template\Packer\Result\Pack;

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
}

class Parser1c {

    protected $debug;
    public $uploadDir, $fileUrls;

    public function __construct($uploadDir,$debug = false)
    {
        $this->debug = $debug;
        $this->uploadDir = $uploadDir;
        $this->fileUrls = array();
        if($this->debug) {
            ini_set('error_reporting', E_ALL);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            $this->write2log('####### Инициализация парсера #######', '__construct');
        }
        if(!empty($this->uploadDir)) {
            if(strpos($this->uploadDir, $_SERVER['DOCUMENT_ROOT']) === false) {
                $this->uploadDir = $_SERVER['DOCUMENT_ROOT'].$this->uploadDir;
            }
            if((mb_substr($this->uploadDir, -1)) !== '/') {
                $this->uploadDir .= '/';
            }
            if(!is_dir($this->uploadDir)) {
                $this->getError('Каталог <b>'.$this->uploadDir.'</b>  не найден.');
            }

        } else {
            $this->getError('Путь до каталога не должен быть пустым');
        }
        $this->start();
    }

    private function start () {
        $scanDir = scandir($this->uploadDir);
        if(count($scanDir) < 3) {
            $this->getError('Каталог <b>'.$this->uploadDir.'</b> пуст.');
        }
        foreach ($scanDir as $dirFile) {
            switch ($dirFile) {
                case "managers.xml":
                    $this->fileUrls['managers'] = $this->uploadDir.'managers.xml';
                    break;
                case "available.xml":
                    $this->fileUrls['available'] = $this->uploadDir.'available.xml';
                    break;
                case "clients.xml":
                    $this->fileUrls['clients'] = $this->uploadDir.'clients.xml';
                    break;
                case "prices.xml":
                    $this->fileUrls['prices'] = $this->uploadDir.'prices.xml';
                    break;
            }
        }
        if(count($this->fileUrls) == 0) {
            $this->getError('Каталог не содержит файлов для импорта');
        }
    }

    public function importManagers() {
        if(!empty($this->fileUrls['managers'])) {
            $this->write2log('Загрузка менеджеров', 'importManagers');
            $xmlDoc = simplexml_load_file($this->fileUrls['managers']);
            if($xmlDoc) {
                $managers = $this->xml2array($xmlDoc);
                foreach ($managers['Менеджер'] as $mKey=>$manager) {
                    if(!empty($manager['@attributes']['Ид'])) {
                        $managerGuid = $manager['@attributes']['Ид'];
                        $managerEmail = $manager['АдресЭлПочты'];
                        $params['email'] = $managerEmail;
                        if ($managerId = $this->getManager($params)) {
                            if($this->debug) {
                                $this->write2log('Найден менеджер ' . $managerEmail, 'importManagers');
                            }
                            $guid = get_the_author_meta('guid_1c', $managerId);
                            if (empty($guid) || $guid != $managerGuid) {
                                $metaData = array(
                                    'guid_1c' => $managerGuid
                                );
                                $this->updateUserMeta($managerId,$metaData);
                            }
                        } else {
                            $username = explode('@',$managerEmail)[0];
                            $password = $username; //Исправить (переделать генерацию пароль и отправку email-ла менеджеру)

                            $managerId = wp_create_user( $username, $password, $managerEmail );
                            if ( is_wp_error( $managerId ) ) {
                                $this->getError($managerId->get_error_message());
                            }
                            else {
                                if($this->debug) {
                                    $this->write2log('Зарегистрирован новый менеджер ' . $managerEmail, 'importManagers');
                                }
                                $userData = array(
                                    'ID' => $managerId,
                                    'role'   => 'shop_manager',
                                );
                                $this->updateUserData($userData);
                            }
                        }
                    }
                }
            } else {
                $this->getError('Ошибка при чтении файла: <b>'.$this->fileUrls['managers'].'</b>');
            }
            $this->write2log('Профили менеджеров загружены и обновлены', 'importManagers');
        }
    }

    public function importClients($gKey = 0) {
        if(!empty($this->fileUrls['clients'])) {
            $this->write2log('Загрузка групп клиентов ', 'importClients');
            $xmlDoc = simplexml_load_file($this->fileUrls['clients']);
            if ($xmlDoc) {
                $clientsGroups = $this->xml2array($xmlDoc);
                $clientsGroupArray = $clientsGroups['ГруппаКлиентов'][$gKey];
					//Обработка групп
                    $groupGuid = $clientsGroupArray['@attributes']['Ид'];
                    $params = array(
                        'meta_key' => 'client_group-guid',
                        'meta_value' => $groupGuid,
                        'post_type'   => 'icm_client_group',
                    );
                    $groupId = $this->getClientsGroup($params);
                    if($groupId) {
                        if($this->debug) {
                            $this->write2log('Найдена группа клиентов ' . $groupId, 'importClients');
                        }
                        $postData = array(
                            'client_group-guid' => $groupGuid,
                            'client_group-name' => $clientsGroupArray['Наименование'],
                        );
                        if(!empty($clientsGroupArray["ОсновнойМенеджер"])) {
                            $params = array(
                                'meta_key' => 'guid_1c',
                                'meta_value' => $clientsGroupArray["ОсновнойМенеджер"]
                            );
                            $postData['client_group-manager'] = $this->getManager($params);
                        } else {
                            $postData['client_group-manager'] = '';
                        }
                        $this->updatePostMeta($groupId, $postData);
                    } else {
                        $postData = array(
                            'post_title'    => $clientsGroupArray['Наименование'].' '.$groupGuid,
                            'post_status'   => 'publish',
                            'post_author'   => 1,
                            'post_type' => 'icm_client_group',
                            'meta_input'    => array(
                                'client_group-guid' => $groupGuid,
                                'client_group-name' => $clientsGroupArray['Наименование'],
                            )
                        );
                        if(!empty($clientsGroupArray["ОсновнойМенеджер"])) {
                            $params['guid'] = $clientsGroupArray["ОсновнойМенеджер"];
                            $postData['meta_input']['client_group-manager'] = $this->getManager($params);
                        }

                        $groupId = wp_insert_post( wp_slash($postData) );
                        if( is_wp_error($groupId) ){
                            $this->getError($groupId->get_error_message());
                        } else {
                            if($this->debug) {
                                $this->write2log('Добавили новую группу клиентов ' . $groupId, 'importClients');
                            }
                        }
                    }

                    //Обработка клиентов в группе
                    if(!empty($groupId)) {
                        $clients = $clientsGroupArray['Клиенты'];
                        foreach ($clients as $client) {
                            $clientGuid = (!empty($client['@attributes']['Ид'])) ? $client['@attributes']['Ид'] : false;
                            $clientEmail = (!empty($client['АдресЭлПочты'])) ? $client['АдресЭлПочты'] : false;

                            if(strpos($clientEmail,' , ') !== false) {
                                $clientEmail = explode(' , ',$clientEmail)[0];
                            }
                            if (filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
                                if ($clientGuid) {
                                    $params = array(
                                        'meta_key' => 'guid_1c',
                                        'meta_value' => $clientGuid,
                                    );
                                    $clientId = $this->getClient($params); //Ищем по guid
                                    if (!$clientId && $clientEmail) {
                                        $clientId = $this->getClient(array( //Ищем по email
                                            'email' => $clientEmail,
                                        ));
                                    }
                                    if ($clientId) { //Клиент найден
                                        if($this->debug) {
                                            $this->write2log('Найден клиент ' . $clientEmail, 'importClients');
                                        }
                                        $userData = array(
                                            'ID' => $clientId,
                                            'role' => 'corporate_body',
                                            'first_name' => $client['Наименование'],
                                            'user_email' => $clientEmail
                                        );
                                        $this->updateUserData($userData);

                                        $metaData = array(
                                            'guid_1c' => $clientGuid,
                                            'guid_group_1c' => $groupGuid,
                                            'user_inn' => $client['ИНН'],
                                            'user_kpp' => $client['КПП'],
                                        );
                                        $this->updateUserMeta($clientId, $metaData);
                                    } else { //Клиент не найден
                                        $username = $clientEmail;
                                        $password = time();

                                        $clientId = wp_create_user($username, $password, $clientEmail);
                                        if (is_wp_error($clientId)) {
                                            $this->getError($clientId->get_error_message());
                                        } else {
                                            if ($this->debug) {
                                                $this->write2log('Зарегистрирован новый клиент ' . $clientEmail, 'importClients');
                                            }
                                            $userData = array(
                                                'ID' => $clientId,
                                                'role' => 'corporate_body',
                                                'first_name' => $client['Наименование'],
                                            );
                                            $this->updateUserData($userData);

                                            $metaData = array(
                                                'guid_group_1c' => $groupGuid,
                                                'guid_1c' => $clientGuid,
                                                'user_inn' => $client['ИНН'],
                                                'user_kpp' => $client['КПП'],
                                            );
                                            $this->updateUserMeta($clientId, $metaData);
                                        }
                                    }


                                    //Обработка документов
                                    $clientsDocs = $client['Документы']['Документ'];
                                    if(!empty($clientsDocs)) {
                                        foreach ($clientsDocs as $clientsDoc) {
                                            if (!empty($clientsDoc['Номер'])) {
                                                $clientsDocData = array(
                                                    'finance_client-id' => $clientId,
                                                    'finance_docs-date' => ($clientsDoc['Дата']) ?: '',
                                                    'finance_docs-number' => $clientsDoc['Номер'],
                                                    'finance_docs-debit' => ($clientsDoc['Дебет']) ?: '',
                                                    'finance_docs-credit' => ($clientsDoc['Кредит']) ?: '',
                                                );

                                                $params = array(
                                                    'meta_key' => 'finance_docs-number',
                                                    'meta_value' => $clientsDocData['finance_docs-number']
                                                );

                                                $docId = $this->getClientDocument($params);

                                                if ($docId) {
                                                    if ($this->debug) {
                                                        $this->write2log('Найден документ ' . $docId, 'importClients');
                                                    }
                                                    $this->updatePostMeta($docId, $clientsDocData);
                                                } else {
                                                    $postData = array(
                                                        'post_title' => 'Документ №' . $clientsDocData['finance_docs-number'],
                                                        'post_status' => 'publish',
                                                        'post_author' => 1,
                                                        'post_type' => 'icm_finance',
                                                        'meta_input' => $clientsDocData
                                                    );
                                                    $groupId = wp_insert_post(wp_slash($postData));
                                                    if (is_wp_error($groupId)) {
                                                        $this->getError($groupId->get_error_message());
                                                    } else {
                                                        if ($this->debug) {
                                                            $this->write2log('Добавили новый документ' . $groupId, 'importClients');
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

							// Обработка точек самовывоза
							$pickupArray = $client['ТочкиДоставки'];
							foreach ($pickupArray as $pickup) {
								if (!empty($pickup) && is_string($pickup['Геоточка'])) {
									$pickupId = $this->getPickupID($pickup['Геоточка']);
									$managerParams = array(
										'meta_key' => 'guid_1c',
										'meta_value' => $clientsGroupArray["ОсновнойМенеджер"]
									);
									$pickupData = [
										"address" =>  $pickup['@attributes']["Адрес"],
										"coords" => $pickup['Геоточка'],
										"manager" => $this->getManager($managerParams),
										"company_name" => $clientsGroupArray['Наименование'],
										"phone" => $client['ТелефонДоставки'],
									];
									$this->updatePickup($pickupId, $pickupData);
								}
							}
                        }
                    }
                    if(!empty($clientsGroups['ГруппаКлиентов'][$gKey + 1])) {
                        return array(
                            'gKey' => $gKey+1,
                            'data' => $clientsGroups['ГруппаКлиентов'][$gKey + 1]
                        );
                    } else {
                        return false;
                    }
            }
            else {
                $this->getError('Ошибка при чтении файла: <b>'.$this->fileUrls['clients'].'</b>');
            }
            $this->write2log('Группы клиентов загружены и обновлены ', 'importClients');
        } else {
            return false;
        }
    }

    public function importPrices($gKey = 0) {
        global $wpdb;
        if(!empty($this->fileUrls['prices'])) {
            $this->write2log('Загрузка цен', 'importPrices');
            print $this->fileUrls['prices'];
            $xmlDoc = simplexml_load_file($this->fileUrls['prices']);
            if($xmlDoc) {
                $gIter = 0;
                $gKeyNext = false;
                /**
                 * Проходим по группам, группа соответствующая заданному $gKey - уходит в обработку,
                 * если есть следующая после выбранной, то присваиваем $gKeyNext значение true
                 */
                foreach ($xmlDoc as $group) {
                    if ($gIter == $gKey) {
                        $groupPrice = $group;
                    } else {
                        if($gIter == ($gKey + 1)) {
                            $gKeyNext = true;
                            break;
                        }
                    }
                    $gIter += 1;
                }
                $groupPrice = $this->xml2array($groupPrice);
                $groupGuid = $groupPrice['@attributes']['Ид'];
                $params = array(
                    'meta_key' => 'client_group-guid',
                    'meta_value' => $groupGuid,
                    'post_type'   => 'icm_client_group',
                );
                $groupId = $this->getClientsGroup($params);
                if($groupId) {
                    $productInsert = 0;
                    $productUpdate = 0;
                    $productFound = 0;
                    $productCount = 0;
                    foreach ($groupPrice['ПрайсЛист']['СтрокаПрайса'] as $docPrice) {
                        $productCount += 1;
                        $docPriceGuid = $docPrice['Номенклатура']['@attributes']['Ид'];
                        $params = array(
                            'meta_key' => '_id_1c',
                            'meta_value' => $docPriceGuid,
                        );
                        $productId = $this->getProduct($params);
                        if ($productId) {
                            $productFound += 1;
                            /**
                             * Таблица wp_icm_products_prices:
                             * product_id - int
                             * group_id - int
                             * price - double
                             * relevance - date
                             */
                            $price = array(
                                'product_id' => (int)$productId,
                                'group_id' => (int)$groupId,
                                'value' => str_replace(',', '.', $docPrice['Цена']),
                                'relevance' => date('Y-m-d', strtotime($docPrice['Актуальность']))
                            );

                            $priceRes = $wpdb->get_results("SELECT * FROM wp_icm_products_prices WHERE product_id = {$price['product_id']} AND group_id = {$price['group_id']}", ARRAY_A);
                            if (empty($priceRes)) {
                                $productInsert += 1;
                                $wpdb->get_results("INSERT INTO wp_icm_products_prices VALUES (NULL,{$price['product_id']},{$price['group_id']},{$price['value']},'{$price['relevance']}')");
                                if ($wpdb->last_error !== '') :
                                    $wpdb->print_error();
                                endif;
                            } else {
                                $productUpdate += 1;
                                if (strtotime($priceRes[0]['relevance']) < strtotime($price['relevance'])) {
                                    $wpdb->get_results("UPDATE wp_icm_products_prices SET price = {$price['value']}, relevance = {$price['relevance']} WHERE id = {$priceRes[0]['id']})");
                                    if ($wpdb->last_error !== '') :
                                        $wpdb->print_error();
                                    endif;
                                }
                            }
                        }
                    }
                    echo '<p>Найдено товаров в каталоге '.$productFound.' из '.$productCount.'</p>';
                    echo '<p>Добавлено '.$productInsert.'</p>';
                    echo '<p>Обновлено '.$productUpdate.'</p>';
                } else {
                    echo 'Группа с guid '.$groupPrice['@attributes']['Ид'].' в магазине не найдена'.'<br>';
                }
                if($gKeyNext) {
                    return array(
                        'gKey' => $gKey + 1,
                        'data' => $groupPrice
                    );
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    public function importAvailable() {
        global $wpdb;
        if(!empty($this->fileUrls['available'])) {
            $this->write2log('Загрузка отгрузок', 'importAvailable');
            $xmlDoc = simplexml_load_file($this->fileUrls['available']);
            if($xmlDoc) {
                $xmlDoc = $this->xml2array($xmlDoc);
				foreach ($xmlDoc['ГруппаКлиентов'] as $clientsGroup) {
					$clientsGroupID = $clientsGroup['@attributes']['Ид'];
					$gParams = array(
						'meta_key' => 'client_group-guid',
						'meta_value' => $clientsGroupID,
						'post_type'   => 'icm_client_group',
					);
					$groupId = $this->getClientsGroup($gParams);
					if ($groupId && is_string($clientsGroup['ТочкаДоставки']['Геоточка'])) {
						$deliveryPoint = array(
							'address' => $clientsGroup['ТочкаДоставки']['@attributes']['Адрес'],
							'geoPoint' => $clientsGroup['ТочкаДоставки']['Геоточка'],
						);

						if (isset($clientsGroup['ТочкаДоставки']['ДоступностьТоваров']['ДоступныйТовар'])) {
							foreach ($clientsGroup['ТочкаДоставки']['ДоступностьТоваров']['ДоступныйТовар'] as $product) {
								if (isset($product['Номенклатура']['@attributes']['Ид'])) {
									$pParams = array(
										'meta_key' => '_id_1c',
										'meta_value' => $product['Номенклатура']['@attributes']['Ид'],
									);
								}
								$productId = $this->getProduct($pParams);
								if ($productId && isset($product['Отгружено']) && isset($product['ДатаПоследнейПоставки'])) {
									$aParams = [
										'available_product' => $productId,
										'available_geo' => $deliveryPoint["geoPoint"]
									];
									$availableID = $this->getAvailable($aParams);

									$availableData = array(
										'clientGroup' => $groupId,
										'available_address' => $deliveryPoint['address'],
										'available_geo' => ($deliveryPoint['geoPoint']) ?: '',
										'available_product' => $productId,
										'available_count' => $product['Отгружено'],
										'available_data' => $product['ДатаПоследнейПоставки']
									);

									if ($availableID) {
										$this->updatePostMeta($availableID, $availableData);
									} else {
										$postData = array(
											'post_title' => 'Отгрузка для ' . $clientsGroupID,
											'post_status' => 'publish',
											'post_author' => 1,
											'post_type' => 'icm_available',
											'meta_input' => $availableData
										);
										$availableID = wp_insert_post(wp_slash($postData));
										if (is_wp_error($availableID)) {
											$this->getError($availableID->get_error_message());
										}
									}
								}
							}
						}
					}
				}
            }
        }
    }

    private function getAvailable($params) {
		$args = [
			"post_type" => "icm_available",
			"meta_query" => [
				"relation" => "AND"
			]
 		];
		foreach ($params as $key => $value) {
			$args["meta_query"][] = [
				"key" => $key,
				"value" => $value
			];
		}
        $available = get_posts($args);
        if(!empty($available)) {
            return $available[0]->ID;
        } else {
            return false;
        }
    }

    private function getProduct($params) {
        $params['post_type'] = 'product';
        $products = get_posts($params);
        if(!empty($products)) {
            return $products[0]->ID;
        } else {
            return false;
        }
    }

    private function getClientDocument($params) {
        $document = get_posts(array_merge(array('post_type'   => 'icm_finance',),$params));
        if(!empty($document)) {
            return $document[0]->ID;
        } else {
            return false;
        }
    }

    private function getClient($params) {
        if(empty($params['email'])) {
            $client = get_users($params);
        } else {
            $client = get_users('search='.$params['email']);
        }
        if(!empty($client)) {
            return $client[0]->ID;
        } else {
            return false;
        }
    }

    private function getManager($params) {
        if(empty($params['email'])) {
            $manager = get_users($params);
        } else {
            $manager = get_users('search='.$params['email']);
        }
        if(!empty($manager)) {
            return $manager[0]->ID;
        } else {
            return false;
        }
    }
    
    private function updateUserData ($data) {
        wp_update_user($data);
        if($this->debug) {
            $this->write2log('Обновили карточку клиента '.$data['ID'], 'updateUserData');
        }
    }

    private function updateUserMeta ($userId, $data) {
        $postMeta = get_user_meta( $userId );

        foreach ($data as $key=>$value) {
            if(!is_array($value)) {
                if(!empty($postMeta[$key][0])) {
                    if($postMeta[$key][0] != $value) {
                        update_user_meta($userId, $key, sanitize_text_field($value));
                        if ($this->debug) {
                            $this->write2log('Обновили для пользователя ' . $userId . ' поле ' . $key . '. Новое значение = ' . $value, 'updateUserMeta');
                        }
                    }
                } else {
                    if(!empty($value)) {
                        update_user_meta($userId, $key, sanitize_text_field($value));
                        if ($this->debug) {
                            $this->write2log('Обновили для пользователя ' . $userId . ' поле ' . $key . '. Новое значение = ' . $value, 'updateUserMeta');
                        }
                    }
                }
            }
        }
    }

    private function updatePostMeta ($postId, $data) {
        $postMeta = get_post_meta( $postId );

        foreach ($data as $key=>$value) {
            if(!is_array($value)) {
                if(!empty($postMeta[$key][0]) && $postMeta[$key][0] != $value) {
                    update_post_meta( $postId, $key, $value);
                    if($this->debug) {
                        $this->write2log('Обновили для поста '.$postId.' поле ' . $key.'. Новое значение = '.$value, 'updatePostMeta');
                    }
                }
            }
        }
    }

	private function updatePickup($id, $data) {
		$pickupArgs = [
			"post_type" => "pickup",
			"post_title" => $data["address"],
			"post_status" => "publish"
		];
		if ($id)
			$pickupArgs["ID"] = $id;

		$id = wp_insert_post($pickupArgs);
		update_field("coords", $data["coords"], $id);
		update_field("fio-yuridicheskogo-lica", $data["company_name"], $id);
		update_field("telefon-yuridicheskogo-lica", $data["phone"], $id);
		update_field("magazin", $data["manager"], $id);
		update_field("active_point", "1", $id);
	}

	private function getPickupID($geo) {
		$pickupArgs = [
			"post_type" => "pickup",
			"meta_query" => [
				[
					"key" => "coords",
					"value" => $geo
				]
			]
		];
		$pickupQuery = new \WP_Query($pickupArgs);
		if ($pickupQuery->have_posts()) {
			$pickupQuery->the_post();
			$id = get_the_ID();
			wp_reset_postdata();
			return $id;
		} else {
			return false;
		}
	}

    private function getClientsGroup($params) {
        $groups = get_posts(array_merge(array('post_type'   => 'icm_client_group',),$params));
        if(!empty($groups)) {
            return $groups[0]->ID;
        } else {
            return false;
        }
    }

    protected function getError($message) {
        echo json_encode(array(
            'status' => 'error',
            'message' => $message
        ));
        die();
    }

    private function xml2array($xmlObject) {
        $json = json_encode($xmlObject);
        return json_decode($json,TRUE);
    }

    private function write2log($message,$function) {
        $logFile = fopen('parserLog.txt','a+');
        fwrite($logFile, '['.date('d.m.Y G:i:s').'] '.$function.'()'.' - '.$message.PHP_EOL);
        fclose($logFile);
    }
}