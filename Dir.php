<?php
/**
 * Class that handle the directories
 * 
 * @package gdphp
 * @author  Sheldon Led <sheldonled.ms@gmail.com>
 */
class Dir {
	
	public static function listFiles($folderId = false){
		$cfgfile = Utils::getConfigFile();
		if(is_string($cfgfile))
			return $cfgfile;

		$client = Utils::getGoogleClient();

		if(is_string($client))
			return $client;

		$service = new Google_Service_Drive($client);

		if(!$folderId)
			$folderId = $cfgfile["folder"];

		$pageToken = NULL;
		$fileList = array();
		$folderId = (is_null($folderId) ? $service->about->get()->getRootFolderId() : $folderId);

		do {
			try {
				$parameters = array();
				if ($pageToken) {
				$parameters['pageToken'] = $pageToken;
				}

				$children = $service->children->listChildren($folderId, $parameters);

				foreach ($children->getItems() as $child) {
					$fileId = $child->getId();
					$fileList[$fileId] = "true";
					/*$file = $service->files->get($fileId);
					$fileList[$fileId] = [
						"name" => $file->getTitle(),
						"type" => $file->getMimeType(),
						"downloadUrl" => $file->getDownloadUrl()
					];*/
				}
				$pageToken = $children->getNextPageToken();
			} catch (Exception $e) {
				return "An error occurred: " . $e->getMessage();
				$pageToken = NULL;
			}
		} while ($pageToken);
		return $fileList;
	}

}