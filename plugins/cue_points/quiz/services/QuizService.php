<?php
/**
 * Allows user to handle quizzes
 *
 * @service quiz
 * @package plugins.quiz
 * @subpackage api.services
 */

class QuizService extends KalturaBaseService
{

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if(!QuizPlugin::isAllowedPartner($this->getPartnerId()))
		{
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, QuizPlugin::PLUGIN_NAME);
		}
	}

	/**
	 * Allows to add a quiz to an entry
	 *
	 * @action add
	 * @param string $entryId
	 * @param KalturaQuiz $quiz
	 * @return KalturaQuiz
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_USER_ID
	 * @throws KalturaQuizErrors::PROVIDED_ENTRY_IS_ALREADY_A_QUIZ
	 */
	public function addAction( $entryId, KalturaQuiz $quiz )
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ( !is_null( QuizPlugin::getQuizData($dbEntry) ) )
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_ALREADY_A_QUIZ, $entryId);

		return $this->validateAndUpdateQuizData( $dbEntry, $quiz );
	}

	/**
	 * Allows to update a quiz
	 *
	 * @action update
	 * @param string $entryId
	 * @param KalturaQuiz $quiz
	 * @return KalturaQuiz
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_USER_ID
	 * @throws KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ
	 */
	public function updateAction( $entryId, KalturaQuiz $quiz )
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		$kQuiz = QuizPlugin::validateAndGetQuiz( $dbEntry );
		return $this->validateAndUpdateQuizData( $dbEntry, $quiz, $kQuiz->getVersion(), $kQuiz );
	}

	/**
	 * if user is entitled for this action will update quizData on entry
	 * @param entry $dbEntry
	 * @param KalturaQuiz $quiz
	 * @param int $currentVersion
	 * @param kQuiz|null $newQuiz
	 * @return KalturaQuiz
	 * @throws KalturaAPIException
	 */
	private function validateAndUpdateQuizData( entry $dbEntry, KalturaQuiz $quiz, $currentVersion = 0, kQuiz $newQuiz = null )
	{
		if ( !QuizPlugin::validateUserEntitledForQuizEdit($dbEntry) ) {
			KalturaLog::debug('Update quiz allowed only with admin KS or entry owner or co-editor');
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}
		$quizData = $quiz->toObject($newQuiz);
		$quizData->setVersion( $currentVersion+1 );
		QuizPlugin::setQuizData( $dbEntry, $quizData );
		$dbEntry->setIsTrimDisabled( true );
		$dbEntry->save();
		$quiz->fromObject( $quizData );
		return $quiz;
	}

	/**
	 * Allows to get a quiz
	 *
	 * @action get
	 * @param string $entryId
	 * @return KalturaQuiz
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ
	 */
	public function getAction( $entryId )
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$kQuiz = QuizPlugin::getQuizData($dbEntry);
		if ( is_null( $kQuiz ) )
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $entryId);

		$quiz = new KalturaQuiz();
		$quiz->fromObject( $kQuiz );
		return $quiz;
	}

	/**
	 * List quiz objects by filter and pager
	 *
	 * @action list
	 * @param KalturaQuizFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaQuizListResponse
	 */
	public function listAction(KalturaQuizFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaQuizFilter;

		if (! $pager)
			$pager = new KalturaFilterPager ();

		return $filter->getListResponse($pager, $this->getResponseProfile());
	}

	/**
	 * 
	 * @action getQuizQuestionsPDF
	 * @param string $entryId
	 * @return string 
	 * @throws KalturaAPIException
	 */
	public function getQuizQuestionsPDFAction($entryId)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		$kQuiz = QuizPlugin::validateAndGetQuiz($dbEntry);


		$pdf=new PDF("P","in");
		$pdf->SetMargins(1,1,1);
		$pdf->AddPage();
		$pdf->SetFont('Times','',12);
		$pdf->SetFillColor(240, 100, 100);
		$pdf->SetFont('Times','BU',12);
		$pdf->Cell(0, .25, "Quiz [".$dbEntry->getName()."] questions", 1, 2, "C", 1);

		$questionType = QuizPlugin::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_QUESTION);
		$questions = CuePointPeer::retrieveByEntryId($entryId, array($questionType));
		foreach ($questions as $question)
		{
			/**
			 * @var QuestionCuePoint $question
			 */
			$pdf->SetFont('Times','',15);
			$pdf->MultiCell(0, 0.25, $question->getName(), 'LR', "L");
			foreach ($question->getOptionalAnswers() as $optionalAnswer)
			{
				/**
				 * @var kOptionalAnswer $optionalAnswer
				 */
				$pdf->SetFont('Times','',8);
				$pdf->MultiCell(0, 0.15, $optionalAnswer->getText(), 'LR', "L");
			}
		}



/*
		$lipsum1="Lorem ipsum dolor sit amet, nam aliquam dolore est, est in eget.";

		$lipsum2="Nibh lectus, pede fusce ullamcorper vel porttitor.";

		$lipsum3 ="Duis maecenas et curabitur, felis dolor.";

		$pdf->SetFont('Times','',12);
//MultiCell(float w, float h, string txt [, mixed border [, string align [, boolean fill]]])
		$pdf->MultiCell(0, 0.5, $lipsum1, 'LR', "L");
		$pdf->MultiCell(0, 0.25, $lipsum2, 1, "R");
		$pdf->MultiCell(0, 0.15, $lipsum3, 'B', "J");

		$pdf->AddPage();
		$pdf->Write(0.5, $lipsum1.$lipsum2.$lipsum3);*/

		$pdf->Output('/tmp/outQuiz.pdf','F');
		return "this works";
		
	}

}