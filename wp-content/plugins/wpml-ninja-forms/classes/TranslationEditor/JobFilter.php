<?php

namespace WPML\Forms\NinjaForms\TranslationEditor;

use WPML\FP\Str;
use WPML\FP\Obj;

class JobFilter implements \IWPML_Backend_Action, \IWPML_Frontend_Action {
	const NINJA_FORMS_TOP_LEVEL_GROUP_ID = 'ninja-forms';

	const NINJA_FORMS_TOP_LEVEL_GROUP_TITLE = 'Ninja Forms';

	const JOB_ORIGINAL_POST_TYPE_NINJA_FORMS = 'package_ninja-forms';

	const SLUG_TO_WORD = [
		'msg'  => 'message',
		'sub'  => 'submissions',
		'desc' => 'Description',
		'num'  => '',
	];

	const GROUP_SETTING = 'setting';

	const GROUP_SUCCESS_MGS = 'success_msg';

	const GROUP_EMAIL = 'email';

	const GROUP_NAME_FIELD_BLOCK = 'Field Block';


	public function add_hooks() {
		add_filter( 'wpml_tm_adjust_translation_fields', [ $this, 'addTitleAndGroupInfo' ], 10, 2 );
		add_filter( 'wpml_tm_adjust_translation_job', [ $this, 'reorderFields' ], 10, 2 );
	}

	private function isNinjaFormJob( \stdClass $job ): bool {
		return self::JOB_ORIGINAL_POST_TYPE_NINJA_FORMS === Obj::prop( 'original_post_type', $job );
	}

	public function reorderFields( array $fields, \stdClass $job ): array {
		if ( ! $this->isNinjaFormJob( $job ) ) {
			return $fields;
		}

		$fields = array_map(
			function ( array $field ) {
				$item = $field['attributes']['id'];
				$id   = (int) ( Str::match( '@[0-9]+@', $item )[0] ?? 0 );

				$field['__sort_helper'] = [
					'id'      => $id,
					'full_id' => $item,
					'part_id' => explode( '-', $item ),
				];

				return $field;

			},
			$fields
		);

		$sortClass = new class() {
			public function sortByGroupId( array $a, array $b ): int {
				return $a['__sort_helper']['id'] <=> $b['__sort_helper']['id'];
			}

			public function settingTitleFirst( array $item ): bool {
				return 'setting-title' === $item['__sort_helper']['full_id'];
			}

			public function labelFirstInGroup( array $item ): bool {
				return 'label' === $item['__sort_helper']['part_id'][0] && ! isset( $item['__sort_helper']['part_id'][2] );
			}

			public function sortElementsInSameGroupByAlphabeticalOrder( array $a, array $b ): int {
				return strcmp( $a['__sort_helper']['full_id'], $b['__sort_helper']['full_id'] );
			}
		};

		usort(
			$fields,
			function ( array $a, array $b ) use ( $sortClass ): int {
				$sortId = $sortClass->sortByGroupId( $a, $b );
				if ( 0 !== $sortId ) {
					return $sortId;
				}

				if ( $sortClass->settingTitleFirst( $a ) ) {
					return - 1;
				}
				if ( $sortClass->settingTitleFirst( $b ) ) {
					return + 1;
				}

				if ( $sortClass->labelFirstInGroup( $a ) ) {
					return - 1;
				}
				if ( $sortClass->labelFirstInGroup( $b ) ) {
					return + 1;
				}

				return $sortClass->sortElementsInSameGroupByAlphabeticalOrder( $a, $b );
			}
		);

		return array_map(
			function ( array $item ): array {
				unset( $item['__sort_helper'] );

				return $item;

			},
			$fields
		);
	}

	public function addTitleAndGroupInfo( array $fields, \stdClass $job ): array {
		if ( ! $this->isNinjaFormJob( $job ) ) {
			return $fields;
		}

		foreach ( $fields as $key => $field ) {
			$fields[ $key ] = $this->processField( $field );
		}

		return $fields;
	}


	private function processField( array $field ): array {
		$fieldTitle = (string) Obj::prop( 'title', $field );

		$parts     = explode( '-', $fieldTitle );
		$inputName = $parts[0] ?? null;
		$inputId   = $parts[1] ?? null;
		$optionId  = $parts[3] ?? null;

		$groupId   = sprintf( 'group_id-%d', $inputId );
		$groupName = '';

		switch ( strtolower( $inputName ) ) {
			case self::GROUP_SETTING:
				$groupId   = 'setting';
				$groupName = 'Setting';

				$labelTitle = $this->convertSlugToHuman( $inputId );
				break;
			case self::GROUP_SUCCESS_MGS:
				$groupId   = self::GROUP_SUCCESS_MGS;
				$groupName = 'Success Message';

				$labelTitle = $this->convertSlugToHuman( $inputName );
				break;
			case self::GROUP_EMAIL:
				$groupId   = sprintf( 'group_email_id-%d', $optionId );
				$groupName = 'E-Mail';

				$labelTitle = $this->convertSlugToHuman( $inputId );
				break;
			default:
				$labelTitle = $this->convertSlugToHuman( $inputName );
				break;
		}

		if ( is_numeric( $inputId ) ) {
			$groupName = self::GROUP_NAME_FIELD_BLOCK;

			if ( is_numeric( $optionId ) ) {
				$labelTitle = sprintf( 'Option %d', ( 1 + $optionId ) );
			}
		}

		return $this->handleField( $field, $labelTitle, $groupId, $groupName );
	}

	private function convertSlugToHuman( string $settingStringId ): string {
		$words = preg_split( '/(?=[A-Z])|_/', $settingStringId, - 1, PREG_SPLIT_NO_EMPTY );

		$returnLable = '';
		$lastWord    = '';
		foreach ( $words as $word ) {
			$word = strtolower( $word );
			$word = self::SLUG_TO_WORD[ $word ] ?? $word;

			if ( ! $word || $lastWord === $word ) {
				continue;
			}
			$lastWord = $word;

			$returnLable .= ucwords( $word ) . ' ';
		}

		return trim( $returnLable );
	}

	private function handleField( array $field, string $title, string $groupId, string $groupName ): array {
		$field['title'] = $title;
		$field['group'] = [
			self::NINJA_FORMS_TOP_LEVEL_GROUP_ID => self::NINJA_FORMS_TOP_LEVEL_GROUP_TITLE,
		];

		if ( $groupName ) {
			$field['group'][ $groupId ] = $groupName;
		}

		return $field;
	}
}
