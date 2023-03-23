{
  inputs.nixpkgs.url = "nixpkgs/nixos-22.11";

  outputs = { self, nixpkgs }:
  let
    inherit (builtins) genList elemAt length listToAttrs;
    supportedSystems = [
      "aarch64-darwin"
      "aarch64-linux"
      "i686-linux"
      "x86_64-darwin"
      "x86_64-linux"
    ];
    shells = listToAttrs (genList (i: let
      system = (elemAt supportedSystems i);
      pkgs = import nixpkgs {
        inherit system;
      };
      php = pkgs.php81.withExtensions ({enabled, all}:
        enabled ++ [ all.rdkafka]
      );
    in {
      name = system;
      value = pkgs.mkShell {
        buildInputs = [
          php
          php.packages.composer
        ];
      };
    }) (length supportedSystems));
  in {
    devShell = shells;
  };
}
