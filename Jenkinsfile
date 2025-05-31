pipeline {
    agent any

    environment {
        // Define la herramienta SonarQube configurada en Jenkins
        SONARQUBE = 'sonarqube'
    }

    stages {
        stage('Clone') {
            steps {
                timeout(time: 2, unit: 'MINUTES') {
                    git branch: 'main',
                        credentialsId: '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',
                        url: 'https://github.com/maykolaracayo22/backend-test.git'
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                timeout(time: 5, unit: 'MINUTES') {
                    sh 'cd reservasback && composer install --no-interaction --prefer-dist --optimize-autoloader'
                }
            }
        }

        stage('Run Tests') {
            steps {
                timeout(time: 10, unit: 'MINUTES') {
                    sh 'cd reservasback && ./vendor/bin/phpunit --configuration phpunit.xml'
                }
            }
        }

        stage('SonarQube Analysis') {
            environment {
                scannerHome = tool 'SonarQubeScanner'
            }
            steps {
                timeout(time: 5, unit: 'MINUTES') {
                    withSonarQubeEnv(SONARQUBE) {
                        sh """
                        cd reservasback && \\
                        ${scannerHome}/bin/sonar-scanner \\
                        -Dsonar.projectKey=backend-test \\
                        -Dsonar.sources=app,routes,database \\
                        -Dsonar.php.coverage.reportPaths=storage/logs/clover.xml \\
                        -Dsonar.host.url=http://tu_sonarqube_url:9000 \\
                        -Dsonar.login=tu_sonar_token
                        """
                    }
                }
            }
        }

        stage('Quality Gate') {
            steps {
                timeout(time: 10, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Deploy') {
            steps {
                echo 'Aquí puedes agregar los comandos para desplegar tu aplicación Laravel.'
            }
        }
    }
}
