{ pkgs, ... }: {
  channel = "stable-24.05";

  packages = [
    (pkgs.php82.withExtensions ({ enabled, all }: enabled ++ [
      all.iconv
      all.mbstring
      all.openssl
      all.pdo
      all.pdo_mysql
      all.tokenizer
      all.ctype
      all.xml
      all.curl
      all.fileinfo
    ]))

    pkgs.php82Packages.composer
    pkgs.nodejs_20
    pkgs.git
  ];

  env = {
    COMPOSER_ALLOW_SUPERUSER = "1";
  };

  idx = {
    previews = {
      enable = true;
    };
  };
}
