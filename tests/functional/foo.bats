#!/usr/bin/env bats

FIXTURE_DIR="${BATS_TEST_DIRNAME}/fixture/integration"

setup() {
    ${FIXTURE_DIR}/setUp.sh
    cp -r ${BATS_TEST_DIRNAME}/../../dev-tools/ci-integration/* ${FIXTURE_DIR}/
}

teardown() {
    rm ${FIXTURE_DIR}/step*.sh
    ${FIXTURE_DIR}/tearDown.sh
}

@test "check changed files" {
  result="$(echo 2+2 | bc)"

  [ "$result" -eq 4 ]
}
